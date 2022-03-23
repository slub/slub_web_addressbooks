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

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to render meta tags
 *
 * # Example: Basic Example: News title as og:title meta tag
 * <code>
 * <n:metaTag property="og:title" content="{newsItem.title}" />
 * </code>
 * <output>
 * <meta property="og:title" content="TYPO3 is awesome" />
 * </output>
 *
 */
class MetaTagViewHelper extends AbstractViewHelper
{
    /**
     * Arguments initialization
     *
     */
    public function initializeArguments()
    {
        $this->registerArgument('property', 'string', 'Property of meta tag', false, '', true);
        $this->registerArgument('content', 'string', 'Content of meta tag', true, null, true);
    }

    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $content = (string)$arguments['content'];

        if ($content !== '') {
            $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
            $pageRenderer->setMetaTag('property', $arguments['property'], $content);
        }
    }
}
