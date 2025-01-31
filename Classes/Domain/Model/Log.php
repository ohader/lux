<?php
declare(strict_types = 1);
namespace In2code\Lux\Domain\Model;

use In2code\Lux\Domain\Repository\LinklistenerRepository;
use In2code\Lux\Domain\Repository\SearchRepository;
use In2code\Luxenterprise\Domain\Model\AbTestingPage;
use In2code\Luxenterprise\Domain\Repository\AbTestingPageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Log
 */
class Log extends AbstractModel
{
    const TABLE_NAME = 'tx_lux_domain_model_log';
    const STATUS_DEFAULT = 0;
    const STATUS_NEW = 1;
    const STATUS_IDENTIFIED = 2; // Fieldlistening
    const STATUS_IDENTIFIED_EMAIL4LINK = 21;
    const STATUS_IDENTIFIED_EMAIL4LINK_SENDEMAIL = 22;
    const STATUS_IDENTIFIED_EMAIL4LINK_SENDEMAILFAILED = 23;
    const STATUS_IDENTIFIED_FORMLISTENING = 25;
    const STATUS_IDENTIFIED_FRONTENDAUTHENTICATION = 26;
    const STATUS_IDENTIFIED_LUXLETTERLINK = 28;
    const STATUS_ATTRIBUTE = 3;
    const STATUS_PAGEVISIT2 = 40;
    const STATUS_PAGEVISIT3 = 41;
    const STATUS_PAGEVISIT4 = 42;
    const STATUS_PAGEVISIT5 = 43;
    const STATUS_DOWNLOAD = 50;
    const STATUS_SEARCH = 55;
    const STATUS_ACTION = 60;
    const STATUS_ACTION_QUEUED = 61;
    const STATUS_CONTEXTUAL_CONTENT = 70;
    const STATUS_LINKLISTENER = 80;
    const STATUS_MERGE_BYFINGERPRINT = 90;
    const STATUS_MERGE_BYEMAIL = 91;
    const STATUS_SHORTENER_VISIT = 100;
    const STATUS_ABTESTING_PAGE = 200;
    const STATUS_ERROR = 900;

    /**
     * @var \In2code\Lux\Domain\Model\Visitor
     */
    protected $visitor = null;

    /**
     * @var int
     */
    protected $status = 0;

    /**
     * @var \DateTime|null
     */
    protected $crdate = null;

    /**
     * @var string
     */
    protected $properties = '';

    /**
     * @return Visitor
     */
    public function getVisitor()
    {
        return $this->visitor;
    }

    /**
     * @param Visitor $visitor
     * @return Log
     */
    public function setVisitor(Visitor $visitor): Log
    {
        $this->visitor = $visitor;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return Log
     */
    public function setStatus(int $status): Log
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCrdate(): \DateTime
    {
        return $this->crdate;
    }

    /**
     * @param \DateTime $crdate
     * @return Log
     */
    public function setCrdate(\DateTime $crdate): Log
    {
        $this->crdate = $crdate;
        return $this;
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return json_decode($this->properties, true);
    }

    /**
     * @param array $properties
     * @return Log
     */
    public function setProperties(array $properties): Log
    {
        $this->properties = json_encode($properties);
        return $this;
    }

    /**
     * @return string
     */
    public function getHref(): string
    {
        return ltrim($this->getPropertyByKey('href'), '/');
    }

    /**
     * @return string
     */
    public function getWorkflowTitle(): string
    {
        return $this->getPropertyByKey('workflowTitle');
    }

    /**
     * @return string
     */
    public function getActionTitle(): string
    {
        return $this->getPropertyByKey('actionTitle');
    }

    /**
     * @return string
     */
    public function getActionExecutionTime(): string
    {
        return $this->getPropertyByKey('executionTime');
    }

    /**
     * @return string
     */
    public function getShownContentUid(): string
    {
        return $this->getPropertyByKey('shownContentUid');
    }

    /**
     * @return string
     */
    public function getPageUid(): string
    {
        return $this->getPropertyByKey('pageUid');
    }

    /**
     * @return string
     */
    public function getShortenerpath(): string
    {
        return $this->getPropertyByKey('path');
    }

    /**
     * @return AbTestingPage|null
     */
    public function getAbTestingPage(): ?AbTestingPage
    {
        $abTestingPageIdentifier = $this->getPropertyByKey('abTestingPage');
        if ($abTestingPageIdentifier > 0) {
            $abTestingPageRepository = GeneralUtility::makeInstance(AbTestingPageRepository::class);
            return $abTestingPageRepository->findByUid($abTestingPageIdentifier);
        }
        return null;
    }

    /**
     * @return Search|null
     */
    public function getSearch(): ?Search
    {
        $searchUid = (int)$this->getPropertyByKey('search');
        $searchRepository = GeneralUtility::makeInstance(SearchRepository::class);
        return $searchRepository->findByIdentifier($searchUid);
    }

    /**
     * @return Linklistener|null
     */
    public function getLinklistener(): ?Linklistener
    {
        $linklistenerUid = (int)$this->getPropertyByKey('linklistener');
        $linklistener = GeneralUtility::makeInstance(LinklistenerRepository::class);
        return $linklistener->findByIdentifier($linklistenerUid);
    }

    /**
     * @param string $key
     * @return string
     */
    protected function getPropertyByKey(string $key): string
    {
        $property = '';
        $properties = $this->getProperties();
        if (array_key_exists($key, $properties)) {
            $property = (string)$properties[$key];
        }
        return $property;
    }

    /**
     * Get all status codes that shows an identification
     *
     * @return array
     */
    public static function getIdentifiedStatus(): array
    {
        return [
            Log::STATUS_IDENTIFIED,
            Log::STATUS_IDENTIFIED_FORMLISTENING,
            Log::STATUS_IDENTIFIED_LUXLETTERLINK,
            Log::STATUS_IDENTIFIED_FRONTENDAUTHENTICATION,
            Log::STATUS_IDENTIFIED_EMAIL4LINK,
        ];
    }
}
