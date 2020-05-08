<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Tracker;

use In2code\Lux\Domain\Model\Page;
use In2code\Lux\Domain\Model\Pagevisit;
use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Domain\Repository\PageRepository;
use In2code\Lux\Domain\Repository\VisitorRepository;
use In2code\Lux\Signal\SignalTrait;
use In2code\Lux\Utility\ObjectUtility;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;

/**
 * Class PageTracker
 */
class PageTracker
{
    use SignalTrait;

    /**
     * @var VisitorRepository|null
     */
    protected $visitorRepository = null;

    /**
     * PageTracker constructor.
     */
    public function __construct()
    {
        $this->visitorRepository = ObjectUtility::getObjectManager()->get(VisitorRepository::class);
    }

    /**
     * @param Visitor $visitor
     * @param int $pageUid
     * @param int $languageUid
     * @param string $referrer
     * @return void
     * @throws Exception
     * @throws IllegalObjectTypeException
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @throws UnknownObjectException
     * @throws \Exception
     */
    public function trackPage(Visitor $visitor, int $pageUid, int $languageUid, string $referrer): void
    {
        if ($this->isTrackingActivated($visitor, $pageUid)) {
            $visitor->addPagevisit($this->getPageVisit($pageUid, $languageUid, $referrer));
            $visitor->setVisits($visitor->getNumberOfUniquePagevisits());
            $this->visitorRepository->update($visitor);
            $this->visitorRepository->persistAll();
            $this->signalDispatch(__CLASS__, 'trackPagevisit', [$visitor]);
        }
    }

    /**
     * @param int $pageUid
     * @param int $languageUid
     * @param string $referrer
     * @return Pagevisit
     * @throws Exception
     */
    protected function getPageVisit(int $pageUid, int $languageUid, string $referrer): Pagevisit
    {
        /** @var Pagevisit $pageVisit */
        $pageVisit = ObjectUtility::getObjectManager()->get(Pagevisit::class);
        $pageRepository = ObjectUtility::getObjectManager()->get(PageRepository::class);
        /** @var Page $page */
        $page = $pageRepository->findByUid($pageUid);
        $pageVisit->setPage($page)->setLanguage($languageUid)->setReferrer($referrer)->setDomain();
        return $pageVisit;
    }

    /**
     * @param Visitor $visitor
     * @param int $pageUid
     * @return bool
     * @throws Exception
     */
    protected function isTrackingActivated(Visitor $visitor, int $pageUid): bool
    {
        return $pageUid > 0 && $visitor->isNotBlacklisted() && $this->isTrackingActivatedInSettings();
    }

    /**
     * Check if tracking of pagevisits is turned on via TypoScript
     *
     * @return bool
     * @throws Exception
     */
    protected function isTrackingActivatedInSettings(): bool
    {
        $configurationService = ObjectUtility::getConfigurationService();
        $settings = $configurationService->getTypoScriptSettings();
        return !empty($settings['tracking']['pagevisits']['_enable'])
            && $settings['tracking']['pagevisits']['_enable'] === '1';
    }
}
