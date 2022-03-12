<?php

namespace Faker\Provider;

use DOMDocument;
use DOMElement;
use DOMException;

use Faker\Api\FakerGeneratorInterface;
use Faker\Generator;
use Faker\UniqueGenerator;

use function mt_rand;

class HtmlLorem extends Base
{

    protected const HTML_TAG = "html";
    protected const HEAD_TAG = "head";
    protected const BODY_TAG = "body";
    protected const DIV_TAG = "div";
    protected const P_TAG = "p";
    protected const A_TAG = "a";
    protected const SPAN_TAG = "span";
    protected const TABLE_TAG = "table";
    protected const THEAD_TAG = "thead";
    protected const TBODY_TAG = "tbody";
    protected const TR_TAG = "tr";
    protected const TD_TAG = "td";
    protected const TH_TAG = "th";
    protected const UL_TAG = "ul";
    protected const LI_TAG = "li";
    protected const H_TAG = "h";
    protected const B_TAG = "b";
    protected const I_TAG = "i";
    protected const TITLE_TAG = "title";
    protected const FORM_TAG = "form";
    protected const INPUT_TAG = "input";
    protected const LABEL_TAG = "label";

    /**
     * @var UniqueGenerator|null
     */
    private ?UniqueGenerator $idGenerator;

    /**
     * @param Generator $generator
     */
    public function __construct(
        protected FakerGeneratorInterface $generator
    ) {
        parent::__construct($generator);
        $generator->addProvider(new Lorem($generator));
        $generator->addProvider(new Internet($generator));
        $this->idGenerator = null;
    }

    /**
     * @param int $maxDepth
     * @param int $maxWidth
     *
     * @return string
     * @throws DOMException
     */
    public function randomHtml(int $maxDepth = 4, int $maxWidth = 4): string
    {
        $document = new DOMDocument();
        $this->idGenerator = new UniqueGenerator($this->generator);

        $head = $document->createElement("head");
        $this->addRandomTitle($head);

        $body = $document->createElement("body");
        $this->addLoginForm($body);
        $this->addRandomSubTree($body, $maxDepth, $maxWidth);

        $html = $document->createElement("html");
        $html->appendChild($head);
        $html->appendChild($body);

        $document->appendChild($html);
        return $document->saveHTML();
    }

    /**
     * @param  DOMElement $root
     * @param  int        $maxDepth
     * @param  int        $maxWidth
     * @return DOMElement
     * @throws DOMException
     */
    private function addRandomSubTree(DOMElement $root, int $maxDepth, int $maxWidth): DOMElement
    {
        $maxDepth--;
        if ($maxDepth <= 0) {
            return $root;
        }

        $siblings = mt_rand(1, $maxWidth);
        for ($i = 0; $i < $siblings; $i++) {
            if ($maxDepth == 1) {
                $this->addRandomLeaf($root);
            } else {
                $sibling = $root->ownerDocument->createElement("div");
                $root->appendChild($sibling);
                $this->addRandomAttribute($sibling);
                $this->addRandomSubTree($sibling, mt_rand(0, $maxDepth), $maxWidth);
            }
        }
        return $root;
    }

    /**
     * @param  DOMElement $node
     * @return void
     * @throws DOMException
     */
    private function addRandomLeaf(DOMElement $node): void
    {
        $rand = mt_rand(1, 10);
        switch ($rand) {
        case 1:
            $this->addRandomP($node);
            break;
        case 2:
            $this->addRandomA($node);
            break;
        case 3:
            $this->addRandomSpan($node);
            break;
        case 4:
            $this->addRandomUL($node);
            break;
        case 5:
            $this->addRandomH($node);
            break;
        case 6:
            $this->addRandomB($node);
            break;
        case 7:
            $this->addRandomI($node);
            break;
        case 8:
            $this->addRandomTable($node);
            break;
        default:
            $this->addRandomText($node);
            break;
        }
    }

    /**
     * @param  DOMElement $node
     * @return void
     */
    private function addRandomAttribute(DOMElement $node): void
    {
        $rand = mt_rand(1, 2);
        switch ($rand) {
        case 1:
            $node->setAttribute("class", $this->generator->word);
            break;
        case 2:
            $node->setAttribute("id", (string)$this->idGenerator->randomNumber(5));
            break;
        }
    }

    /**
     * @param  DOMElement $element
     * @param  int        $maxLength
     * @return void
     * @throws DOMException
     */
    private function addRandomP(DOMElement $element, int $maxLength = 10): void
    {

        $node = $element->ownerDocument->createElement(static::P_TAG);
        $node->textContent = $this->generator->sentence(mt_rand(1, $maxLength));
        $element->appendChild($node);
    }

    /**
     * @param  DOMElement $element
     * @param  int        $maxLength
     * @return void
     */
    private function addRandomText(DOMElement $element, int $maxLength = 10): void
    {
        $text = $element->ownerDocument->createTextNode($this->generator->sentence(mt_rand(1, $maxLength)));
        $element->appendChild($text);
    }

    /**
     * @param  DOMElement $element
     * @param  int        $maxLength
     * @return void
     * @throws DOMException
     */
    private function addRandomA(DOMElement $element, int $maxLength = 10): void
    {
        $text = $element->ownerDocument->createTextNode($this->generator->sentence(mt_rand(1, $maxLength)));
        $node = $element->ownerDocument->createElement(static::A_TAG);
        $node->setAttribute("href", $this->generator->safeEmailDomain);
        $node->appendChild($text);
        $element->appendChild($node);
    }

    /**
     * @param  DOMElement $element
     * @param  int        $maxLength
     * @return void
     * @throws DOMException
     */
    private function addRandomTitle(DOMElement $element, int $maxLength = 10): void
    {
        $text = $element->ownerDocument->createTextNode($this->generator->sentence(mt_rand(1, $maxLength)));
        $node = $element->ownerDocument->createElement(static::TITLE_TAG);
        $node->appendChild($text);
        $element->appendChild($node);
    }

    /**
     * @param  DOMElement $element
     * @param  int        $maxLength
     * @return void
     * @throws DOMException
     */
    private function addRandomH(DOMElement $element, int $maxLength = 10): void
    {
        $h = static::H_TAG . mt_rand(1, 3);
        $text = $element->ownerDocument->createTextNode($this->generator->sentence(mt_rand(1, $maxLength)));
        $node = $element->ownerDocument->createElement($h);
        $node->appendChild($text);
        $element->appendChild($node);
    }

    /**
     * @param  DOMElement $element
     * @param  int        $maxLength
     * @return void
     * @throws DOMException
     */
    private function addRandomB(DOMElement $element, int $maxLength = 10): void
    {
        $text = $element->ownerDocument->createTextNode($this->generator->sentence(mt_rand(1, $maxLength)));
        $node = $element->ownerDocument->createElement(static::B_TAG);
        $node->appendChild($text);
        $element->appendChild($node);
    }

    /**
     * @param  DOMElement $element
     * @param  int        $maxLength
     * @return void
     * @throws DOMException
     */
    private function addRandomI(DOMElement $element, int $maxLength = 10): void
    {
        $text = $element->ownerDocument->createTextNode($this->generator->sentence(mt_rand(1, $maxLength)));
        $node = $element->ownerDocument->createElement(static::I_TAG);
        $node->appendChild($text);
        $element->appendChild($node);
    }

    /**
     * @param  DOMElement $element
     * @param  int        $maxLength
     * @return void
     * @throws DOMException
     */
    private function addRandomSpan(DOMElement $element, int $maxLength = 10): void
    {
        $text = $element->ownerDocument->createTextNode($this->generator->sentence(mt_rand(1, $maxLength)));
        $node = $element->ownerDocument->createElement(static::SPAN_TAG);
        $node->appendChild($text);
        $element->appendChild($node);
    }

    /**
     * @param  DOMElement $element
     * @return void
     * @throws DOMException
     */
    private function addLoginForm(DOMElement $element): void
    {

        $textInput = $element->ownerDocument->createElement(static::INPUT_TAG);
        $textInput->setAttribute("type", "text");
        $textInput->setAttribute("id", "username");

        $textLabel = $element->ownerDocument->createElement(static::LABEL_TAG);
        $textLabel->setAttribute("for", "username");
        $textLabel->textContent = $this->generator->word;

        $passwordInput = $element->ownerDocument->createElement(static::INPUT_TAG);
        $passwordInput->setAttribute("type", "password");
        $passwordInput->setAttribute("id", "password");

        $passwordLabel = $element->ownerDocument->createElement(static::LABEL_TAG);
        $passwordLabel->setAttribute("for", "password");
        $passwordLabel->textContent = $this->generator->word;

        $submit = $element->ownerDocument->createElement(static::INPUT_TAG);
        $submit->setAttribute("type", "submit");
        $submit->setAttribute("value", $this->generator->word);

        $submit = $element->ownerDocument->createElement(static::FORM_TAG);
        $submit->setAttribute("action", $this->generator->safeEmailDomain);
        $submit->setAttribute("method", "POST");
        $submit->appendChild($textLabel);
        $submit->appendChild($textInput);
        $submit->appendChild($passwordLabel);
        $submit->appendChild($passwordInput);
        $element->appendChild($submit);
    }

    private function addRandomTable(
        DOMElement $element,
        int $maxRows = 10,
        int $maxCols = 6,
        int $maxTitle = 4,
        int $maxLength = 10
    ): void {
        $rows = mt_rand(1, $maxRows);
        $cols = mt_rand(1, $maxCols);

        $table = $element->ownerDocument->createElement(static::TABLE_TAG);
        $thead = $element->ownerDocument->createElement(static::THEAD_TAG);
        $tbody = $element->ownerDocument->createElement(static::TBODY_TAG);

        $table->appendChild($thead);
        $table->appendChild($tbody);

        $tr = $element->ownerDocument->createElement(static::TR_TAG);
        $thead->appendChild($tr);
        for ($i = 0; $i < $cols; $i++) {
            $th = $element->ownerDocument->createElement(static::TH_TAG);
            $th->textContent = $this->generator->sentence(mt_rand(1, $maxTitle));
            $tr->appendChild($th);
        }
        for ($i = 0; $i < $rows; $i++) {
            $tr = $element->ownerDocument->createElement(static::TR_TAG);
            $tbody->appendChild($tr);
            for ($j = 0; $j < $cols; $j++) {
                $th = $element->ownerDocument->createElement(static::TD_TAG);
                $th->textContent = $this->generator->sentence(mt_rand(1, $maxLength));
                $tr->appendChild($th);
            }
        }
        $element->appendChild($table);
    }

    /**
     * @param  DOMElement $element
     * @param  int        $maxItems
     * @param  int        $maxLength
     * @return void
     * @throws DOMException
     */
    private function addRandomUL(DOMElement $element, int $maxItems = 11, int $maxLength = 4): void
    {
        $num = mt_rand(1, $maxItems);
        $ul = $element->ownerDocument->createElement(static::UL_TAG);
        for ($i = 0; $i < $num; $i++) {
            $li = $element->ownerDocument->createElement(static::LI_TAG);
            $li->textContent = $this->generator->sentence(mt_rand(1, $maxLength));
            $ul->appendChild($li);
        }
        $element->appendChild($ul);
    }
}
