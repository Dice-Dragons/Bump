<?php

namespace cv6\Bump\Cli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use XF\Cli\Command\AbstractCommand;
use XF\Cli\Command\AddOnActionTrait;
use XF\Repository\AddOnRepository;
use XF\Util\File;
use XF\Util\Hash;
use XF\Util\Json;

use cv6\Bump\Service\VersionCalculator;
use function intval;

class Bump extends AbstractCommand
{
    use AddOnActionTrait;

    private VersionCalculator $versionCalculator;

    protected function configure()
    {
        $this
            ->setName('dd:bump')
            ->setAliases(['cv6:bump'])
            ->setDescription('Bumps the version of the specified add-on based on semantic versioning rules.')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Add-On ID'
            )
            ->addArgument(
                'type',
                InputArgument::OPTIONAL,
                'Bump type: major, minor, patch, or set state (alpha, beta, rc, stable).',
                null
            )
            ->addArgument(
                'state',
                InputArgument::OPTIONAL,
                'Set specific state (alpha, beta, rc, stable) if checking major/minor/patch.',
                null
            )
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'Show what would happen without applying changes.'
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Skip validation of version ID (ignores downgrades)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->versionCalculator = \XF::service('cv6\Bump:VersionCalculator');

        $id = $input->getArgument('id');
        $type = $input->getArgument('type');
        $stateArg = $input->getArgument('state');
        $dryRun = $input->getOption('dry-run');

        $error = null;
        /** @var \XF\AddOn\AddOn $addOn */
        $addOn = $this->checkEditableAddOn($id, $error);
        if (!$addOn) {
            $output->writeln('<error>' . $error . '</error>');
            return 1;
        }

        $currentVersionId = $addOn->version_id;
        $currentVersionString = $addOn->version_string;

        $output->writeln("Current Version: <info>$currentVersionString</info> (ID: <info>$currentVersionId</info>)");

        if ($type === null) {
            $helper = $this->getHelper('question');
            $options = $this->versionCalculator->getAvailableOptions($currentVersionId);

            $question = new ChoiceQuestion(
                'Select the bump type (defaults to patch):',
                $options,
                0
            );
            $question->setErrorMessage('Bump type %s is invalid.');

            $type = $helper->ask($input, $output, $question);
        }

        try {
            $newVersionId = $this->versionCalculator->calculateNewVersionId($currentVersionId, strtolower($type), $stateArg);
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return 1;
        }

        // Generate String
        $newVersionString = \XF::repository(AddOnRepository::class)->inferVersionStringFromId($newVersionId);

        $output->writeln("New Version: <info>$newVersionString</info> (ID: <info>$newVersionId</info>)");

        if ($newVersionId <= $currentVersionId) {
            if ($input->getOption('force')) {
                $output->writeln("<comment>Warning: New version ID is not higher than current. Proceeding due to --force.</comment>");
            } else {
                $output->writeln("<error>New version ID ($newVersionId) is not higher than existing ($currentVersionId). Skipping.</error>");
                return 1;
            }
        }

        if ($dryRun) {
            $output->writeln("<info>Dry run enabled. No changes made.</info>");
            return 0;
        }

        $installed = $addOn->getInstalledAddOn();
        // Update installed record if exists
        if ($installed) {
            $installed->version_id = $newVersionId;
            $installed->version_string = $newVersionString;
            $installed->save();
        }

        // Update addon.json
        $jsonPath = $addOn->getJsonPath();
        $json = $addOn->getJson();
        $json['version_id'] = intval($newVersionId);
        $json['version_string'] = $newVersionString;

        $written = File::writeFile($jsonPath, Json::jsonEncodePretty($addOn->prepareJsonFile($json)), false);
        if ($written) {
            if ($installed) {
                $installed->fastUpdate('json_hash', Hash::hashTextFile($jsonPath, 'sha256'));
            }
            $output->writeln("Updated addon.json and database.");
        } else {
            $output->writeln("<error>Could not write to addon.json</error>");
            return 1;
        }

        return 0;
    }
}
