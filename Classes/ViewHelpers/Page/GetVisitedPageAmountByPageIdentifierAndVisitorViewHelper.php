<?php
declare(strict_types = 1);
namespace In2code\Lux\ViewHelpers\Page;

use Doctrine\DBAL\Exception;
use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Domain\Repository\PagevisitRepository;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class GetVisitedPageAmountByPageIdentifierAndVisitorViewHelper
 */
class GetVisitedPageAmountByPageIdentifierAndVisitorViewHelper extends AbstractViewHelper
{
    /**
     * @var PagevisitRepository
     */
    protected $pagevisitRepository;

    /**
     * GetDateOfLatestPageVisitViewHelper constructor.
     * @param PagevisitRepository $pagevisitRepository
     */
    public function __construct(PagevisitRepository $pagevisitRepository)
    {
        $this->pagevisitRepository = $pagevisitRepository;
    }

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('visitor', Visitor::class, 'visitor', true);
        $this->registerArgument('pageIdentifier', 'int', 'page identifier', true);
    }

    /**
     * @return int
     * @throws Exception
     */
    public function render(): int
    {
        return $this->pagevisitRepository->findAmountPerPageAndVisitor(
            (int)$this->arguments['pageIdentifier'],
            $this->arguments['visitor']
        );
    }
}
