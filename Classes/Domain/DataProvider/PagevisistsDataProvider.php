<?php
declare(strict_types = 1);
namespace In2code\Lux\Domain\DataProvider;

use Exception;
use In2code\Lux\Domain\Repository\PagevisitRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class PagevisistsDataProvider
 */
class PagevisistsDataProvider extends AbstractDynamicFilterDataProvider
{
    /**
     * Set values like:
     *  [
     *      'amounts' => [
     *          50,
     *          88,
     *          33
     *      ],
     *      'titles' => [
     *          'Th',
     *          'Fr',
     *          'Now'
     *      ]
     *  ]
     *
     * @return void
     * @throws Exception
     */
    public function prepareData(): void
    {
        $pagevisitRepository = GeneralUtility::makeInstance(PagevisitRepository::class);
        $intervals = $this->filter->getIntervals();
        $frequency = (string)$intervals['frequency'];
        foreach ($intervals['intervals'] as $interval) {
            $this->data['amounts'][] = $pagevisitRepository->getNumberOfVisitsInTimeFrame(
                $interval['start'],
                $interval['end'],
                $this->filter
            );
            $this->data['titles'][] = $this->getLabelForFrequency($frequency, $interval['start']);
        }
        $this->overruleLatestTitle($frequency);
    }
}
