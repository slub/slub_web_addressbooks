<?php
namespace Slub\SlubWebAddressbooks\ViewHelpers;

/*
 * This file is part of the slub_web_addressbooks extension.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

 use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to get page info
 *
 * # Example: Basic example
 * <code>
 * <si:pageInfo page="123">
 *	<span>123</span>
 * </code>
 * <output>
 * Will output the page record
 * </output>
 *
 * @package TYPO3
 */
class PageInfoViewHelper extends AbstractViewHelper
{
    /**
     * Initialize arguments.
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('uid', 'integer', 'uid of page', true);
        $this->registerArgument('field', 'string', 'field to fetch from page record', false, 'title');
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
      ) {
        $pageUid = $arguments['uid'];
        $field = $arguments['field'];
        if (0 === $uid) {
            $pageUid = $GLOBALS['TSFE']->id;
        }
        $pageRepository = GeneralUtility::makeInstance(\TYPO3\CMS\Frontend\Page\PageRepository::class);
        $page = $pageRepository->getPage($pageUid);

        $output = $page[$field];

        return $output;
    }
}
