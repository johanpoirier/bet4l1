<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 26/10/14
 * Time: 00:26
 */
namespace FeedIo\Rule;

use FeedIo\Feed\NodeInterface;
use FeedIo\RuleAbstract;

class Description extends RuleAbstract
{

    const NODE_NAME = 'description';

    /**
     * @param  NodeInterface $node
     * @param  \DOMElement   $element
     * @return $this
     */
    public function setProperty(NodeInterface $node, \DOMElement $element)
    {
        $node->setDescription($element->nodeValue);

        return $this;
    }

    /**
     * creates the accurate DomElement content according to the $item's property
     *
     * @param  \DOMDocument  $document
     * @param  NodeInterface $node
     * @return \DOMElement
     */
    public function createElement(\DOMDocument $document, NodeInterface $node)
    {
        $description = htmlspecialchars($node->getDescription());

        return $document->createElement($this->getNodeName(), $description);
    }
}
