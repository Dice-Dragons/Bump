<?php

namespace cv6\Bump\Service;

use XF\Service\AbstractService;
use function intval;

class VersionCalculator extends AbstractService
{
    private const STATE_MAP = ['alpha' => 10, 'beta' => 30, 'rc' => 50, 'stable' => 70];
    private const STATE_MAX = ['alpha' => 29, 'beta' => 49, 'rc' => 69, 'stable' => 70];

    public function calculateNewVersionId(int $currentVersionId, string $type, ?string $targetStateArg = null): int
    {
        $major = intval($currentVersionId / 1000000);
        $minor = intval(($currentVersionId / 10000) % 100);
        $patch = intval(($currentVersionId / 100) % 100);
        $stateBlock = $currentVersionId % 100;

        $currentState = $this->getStateFromBlock($stateBlock);

        if (isset(self::STATE_MAP[$type])) {
            $targetState = $type;
            if ($currentState === $targetState) {
                $newStateBlock = $stateBlock + 1;
                if ($newStateBlock > self::STATE_MAX[$targetState]) {
                    throw new \RuntimeException("Cannot increment $targetState: Max level " . self::STATE_MAX[$targetState] . " reached.");
                }
                return $this->composeId($major, $minor, $patch, $newStateBlock);
            } else {
                $newStateBlock = self::STATE_MAP[$targetState];
                $newId = $this->composeId($major, $minor, $patch, $newStateBlock);

                // If the new ID is lower (e.g. Stable -> Alpha), assuming a new dev phase (Minor bump).
                if ($newId < $currentVersionId) {
                    $newId = $this->composeId($major, $minor + 1, 0, $newStateBlock);
                }
                return $newId;
            }
        }

        $nextMajor = $major;
        $nextMinor = $minor;
        $nextPatch = $patch;
        $nextStateBlock = 70;

        if ($targetStateArg) {
            $targetStateArg = strtolower($targetStateArg);
            if (!isset(self::STATE_MAP[$targetStateArg])) {
                throw new \InvalidArgumentException("Invalid state argument: $targetStateArg");
            }
            $nextStateBlock = self::STATE_MAP[$targetStateArg];
        }

        switch ($type) {
            case 'major':
                $nextMajor++;
                $nextMinor = 0;
                $nextPatch = 0;
                break;
            case 'minor':
                $nextMinor++;
                $nextPatch = 0;
                break;
            case 'patch':
                $nextPatch++;
                break;
            default:
                throw new \InvalidArgumentException("Unknown bump type: $type");
        }

        return $this->composeId($nextMajor, $nextMinor, $nextPatch, $nextStateBlock);
    }

    public function getAvailableOptions(int $currentVersionId): array
    {
        $stateBlock = $currentVersionId % 100;
        $currentState = $this->getStateFromBlock($stateBlock);

        $options = ['patch', 'minor', 'major'];

        switch ($currentState) {
            case 'alpha':
                if ($stateBlock < self::STATE_MAX['alpha']) {
                    $options[] = 'alpha';
                }
                $options[] = 'beta';
                $options[] = 'rc';
                $options[] = 'stable';
                break;

            case 'beta':
                if ($stateBlock < self::STATE_MAX['beta']) {
                    $options[] = 'beta';
                }
                $options[] = 'rc';
                $options[] = 'stable';
                break;

            case 'rc':
                if ($stateBlock < self::STATE_MAX['rc']) {
                    $options[] = 'rc';
                }
                $options[] = 'stable';
                break;

            case 'stable':
                $options[] = 'alpha';
                $options[] = 'beta';
                $options[] = 'rc';
                break;
        }

        return $options;
    }

    public function getStateFromBlock(int $block): string
    {
        if ($block >= 10 && $block <= 29) {
            return 'alpha';
        } elseif ($block >= 30 && $block <= 49) {
            return 'beta';
        } elseif ($block >= 50 && $block <= 69) {
            return 'rc';
        } elseif ($block == 70) {
            return 'stable';
        }
        return 'unknown';
    }

    private function composeId(int $major, int $minor, int $patch, int $stateBlock): int
    {
        return ($major * 1000000) + ($minor * 10000) + ($patch * 100) + $stateBlock;
    }
}
