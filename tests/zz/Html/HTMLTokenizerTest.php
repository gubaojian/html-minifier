<?php
namespace zz\Html;
use zz\Html;

class HTMLTokenizerTest extends \PHPUnit_Framework_TestCase {

    public function testEmpty() {
        $html = '';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array();
        $this->assertEquals($expect, $actual);

        $html = '    ';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'Character',
                'data' => '    ',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '    ',
                'state' => array(
                    0 => 'DataState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);
    }

    public function testSingleChar() {
        $html = 'a';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'Character',
                'data' => 'a',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => 'a',
                'state' => array(
                    0 => 'DataState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = 'ab';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'Character',
                'data' => 'ab',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => 'ab',
                'state' => array(
                    0 => 'DataState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);
    }

    protected static function getHtml($tokens) {
        $html = '';
        foreach ($tokens as $token) {
            foreach ($token['state'] as $state) {
                $html .= $state[1];
            }
        }
        return $html;
    }

    public function testDoctype() {

        $html = '<!DOCTYPE html>';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE html>',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '<!DOCTYPE html>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);


        $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPEPublicKeywordState',
                    22 => 'BeforeDOCTYPEPublicIdentifierState',
                    23 => 'DOCTYPEPublicIdentifierDoubleQuotedState',
                    56 => 'AfterDOCTYPEPublicIdentifierState',
                    57 => 'BetweenDOCTYPEPublicAndSystemIdentifiersState',
                    58 => 'DOCTYPESystemIdentifierDoubleQuotedState',
                    108 => 'AfterDOCTYPESystemIdentifierState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);
    }

    public function testTagBlock() {
        $html = '<p>a</p>';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'StartTag',
                'data' => 'p',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '<p>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                ),
            ),
            1 => array(
                'type' => 'Character',
                'data' => 'a',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => 'a',
                'state' => array(
                    0 => 'DataState',
                ),
            ),
            2 => array(
                'type' => 'EndTag',
                'data' => 'p',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '</p>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'EndTagOpenState',
                    3 => 'TagNameState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = '<P>a</P>';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'StartTag',
                'data' => 'p',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '<P>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                ),
            ),
            1 => array(
                'type' => 'Character',
                'data' => 'a',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => 'a',
                'state' => array(
                    0 => 'DataState',
                ),
            ),
            2 => array(
                'type' => 'EndTag',
                'data' => 'p',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '</P>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'EndTagOpenState',
                    3 => 'TagNameState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);
    }

    public function testEntityDecimal() {
        $html = '&#34;';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'Character',
                'data' => '&#34;',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '&#34;',
                'state' => array(
                    0 => 'DataState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);
    }

    public function testEntityNamed() {
        $html = '&amp;';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'Character',
                'data' => '&amp;',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '&amp;',
                'state' => array(
                    0 => 'DataState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = '<p id="&a';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'StartTag',
                'data' => 'p',
                'selfClosing' => false,
                'attributes' => array(
                    0 => array(
                        'name' => 'id',
                        'value' => '&',
                        'quoted' => false,
                    ),
                ),
                'parseError' => false,
                'html' => '<p id="&',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                    3 => 'BeforeAttributeNameState',
                    4 => 'AttributeNameState',
                    6 => 'BeforeAttributeValueState',
                    7 => 'AttributeValueDoubleQuotedState',
                    8 => 'CharacterReferenceInAttributeValueState',
                ),
            ),
            1 => array(
                'type' => 'Character',
                'data' => 'a',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => 'a',
                'state' => array(
                    0 => 'DataState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = '<p id="&amp;">';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'StartTag',
                'data' => 'p',
                'selfClosing' => false,
                'attributes' => array(
                    0 => array(
                        'name' => 'id',
                        'value' => '&amp;',
                        'quoted' => '"',
                    ),
                ),
                'parseError' => false,
                'html' => '<p id="&amp;">',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                    3 => 'BeforeAttributeNameState',
                    4 => 'AttributeNameState',
                    6 => 'BeforeAttributeValueState',
                    7 => 'AttributeValueDoubleQuotedState',
                    8 => 'CharacterReferenceInAttributeValueState',
                    12 => 'AttributeValueDoubleQuotedState',
                    13 => 'AfterAttributeValueQuotedState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = '<p id="aaaaa&amp;aaaa">';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'StartTag',
                'data' => 'p',
                'selfClosing' => false,
                'attributes' => array(
                    0 => array(
                        'name' => 'id',
                        'value' => 'aaaaa&amp;aaaa',
                        'quoted' => '"',
                    ),
                ),
                'parseError' => false,
                'html' => '<p id="aaaaa&amp;aaaa">',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                    3 => 'BeforeAttributeNameState',
                    4 => 'AttributeNameState',
                    6 => 'BeforeAttributeValueState',
                    7 => 'AttributeValueDoubleQuotedState',
                    13 => 'CharacterReferenceInAttributeValueState',
                    17 => 'AttributeValueDoubleQuotedState',
                    22 => 'AfterAttributeValueQuotedState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);
    }

    public function testEntityInvalid() {
        $html = $source = '&';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'Uninitialized',
                'data' => '&',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '&',
                'state' => array(
                    0 => 'DataState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '&&';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'Character',
                'data' => '&&',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '&&',
                'state' => array(
                    0 => 'DataState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '&&&';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'Character',
                'data' => '&&&',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '&&&',
                'state' => array(
                    0 => 'DataState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);
    }

    public function testEntityDecimalInvalid() {
        $html = '&#34';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'Uninitialized',
                'data' => '&',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '&',
                'state' => array(
                    0 => 'DataState',
                ),
            ),
            1 => array(
                'type' => 'Character',
                'data' => '#34',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '#34',
                'state' => array(
                    0 => 'DataState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);
    }

    public function testTag() {
        $html = '<a href="http://example.com/">link</a>';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'StartTag',
                'data' => 'a',
                'selfClosing' => false,
                'attributes' => array(
                    0 => array(
                        'name' => 'href',
                        'value' => 'http://example.com/',
                        'quoted' => '"',
                    ),
                ),
                'parseError' => false,
                'html' => '<a href="http://example.com/">',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                    3 => 'BeforeAttributeNameState',
                    4 => 'AttributeNameState',
                    8 => 'BeforeAttributeValueState',
                    9 => 'AttributeValueDoubleQuotedState',
                    29 => 'AfterAttributeValueQuotedState',
                ),
            ),
            1 => array(
                'type' => 'Character',
                'data' => 'link',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => 'link',
                'state' => array(
                    0 => 'DataState',
                ),
            ),
            2 => array(
                'type' => 'EndTag',
                'data' => 'a',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '</a>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'EndTagOpenState',
                    3 => 'TagNameState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = '<I/>';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'StartTag',
                'data' => 'i',
                'selfClosing' => true,
                'attributes' => array(),
                'parseError' => false,
                'html' => '<I/>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                    3 => 'SelfClosingStartTagState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

    }


    public function testComment() {
        $html = '<!-- comment -->';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'Comment',
                'data' => '<!-- comment -->',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '<!-- comment -->',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    4 => 'CommentStartState',
                    5 => 'CommentState',
                    14 => 'CommentEndDashState',
                    15 => 'CommentEndState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);
    }


    public function testCDATA() {
        $html = '<![CDATA[
  var i = 0;
 //]]>';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'Character',
                'data' => '<![CDATA[
  var i = 0;
 //]]>',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '<![CDATA[
  var i = 0;
 //]]>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'CDATASectionState',
                    27 => 'CDATASectionRightSquareBracketState',
                    28 => 'CDATASectionDoubleRightSquareBracketState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);
    }

    public function testConditionalComment() {
        $html = $source = '<!--[if expression]>
          HTML <![endif]-->';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'Comment',
                'data' => '<!--[if expression]>
          HTML <![endif]-->',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '<!--[if expression]>
          HTML <![endif]-->',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    4 => 'CommentStartState',
                    5 => 'CommentState',
                    46 => 'CommentEndDashState',
                    47 => 'CommentEndState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<![if expression]> HTML <![endif]>';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'Comment',
                'data' => '<![if expression]>',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<![if expression]>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'ContinueBogusCommentState',
                ),
            ),
            1 => array(
                'type' => 'Character',
                'data' => ' HTML ',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => ' HTML ',
                'state' => array(
                    0 => 'DataState',
                ),
            ),
            2 => array(
                'type' => 'Comment',
                'data' => '<![endif]>',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<![endif]>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'ContinueBogusCommentState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);
    }

    public function testTagImg() {
        $html = $source = '<img id="test" src="img" />';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'StartTag',
                'data' => 'img',
                'selfClosing' => true,
                'attributes' => array(
                    0 => array(
                        'name' => 'id',
                        'value' => 'test',
                        'quoted' => '"',
                    ),
                    1 => array(
                        'name' => 'src',
                        'value' => 'img',
                        'quoted' => '"',
                    ),
                ),
                'parseError' => false,
                'html' => '<img id="test" src="img" />',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                    5 => 'BeforeAttributeNameState',
                    6 => 'AttributeNameState',
                    8 => 'BeforeAttributeValueState',
                    9 => 'AttributeValueDoubleQuotedState',
                    14 => 'AfterAttributeValueQuotedState',
                    15 => 'BeforeAttributeNameState',
                    16 => 'AttributeNameState',
                    19 => 'BeforeAttributeValueState',
                    20 => 'AttributeValueDoubleQuotedState',
                    24 => 'AfterAttributeValueQuotedState',
                    25 => 'BeforeAttributeNameState',
                    26 => 'SelfClosingStartTagState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);
    }

    public function testEndOfFile() {
        $html = $source = '<style>end';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'StartTag',
                'data' => 'style',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '<style>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                ),
            ),
            1 => array(
                'type' => 'Character',
                'data' => 'end',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => 'end',
                'state' => array(
                    0 => 'RAWTEXTState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<script>end';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'StartTag',
                'data' => 'script',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '<script>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                ),
            ),
            1 => array(
                'type' => 'Character',
                'data' => 'end',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => 'end',
                'state' => array(
                    0 => 'ScriptDataState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<PLAINTEXT>end';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'StartTag',
                'data' => 'plaintext',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '<PLAINTEXT>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                ),
            ),
            1 => array(
                'type' => 'Character',
                'data' => 'end',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => 'end',
                'state' => array(
                    0 => 'PLAINTEXTState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<DIV>end</';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'StartTag',
                'data' => 'div',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '<DIV>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                ),
            ),
            1 => array(
                'type' => 'Character',
                'data' => 'end',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => 'end',
                'state' => array(
                    0 => 'DataState',
                ),
            ),
            2 => array(
                'type' => 'Character',
                'data' => '</',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '</',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<script><!-- ';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'StartTag',
                'data' => 'script',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '<script>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                ),
            ),
            1 => array(
                'type' => 'Character',
                'data' => '<!-- ',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!-- ',
                'state' => array(
                    0 => 'ScriptDataState',
                    1 => 'ScriptDataLessThanSignState',
                    2 => 'ScriptDataEscapeStartState',
                    3 => 'ScriptDataEscapeStartDashState',
                    4 => 'ScriptDataEscapedDashDashState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<script><!--a-';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'StartTag',
                'data' => 'script',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '<script>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                ),
            ),
            1 => array(
                'type' => 'Character',
                'data' => '<!--a-',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!--a-',
                'state' => array(
                    0 => 'ScriptDataState',
                    1 => 'ScriptDataLessThanSignState',
                    2 => 'ScriptDataEscapeStartState',
                    3 => 'ScriptDataEscapeStartDashState',
                    4 => 'ScriptDataEscapedDashDashState',
                    5 => 'ScriptDataEscapedState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<script><!--';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'StartTag',
                'data' => 'script',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '<script>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                ),
            ),
            1 => array(
                'type' => 'Character',
                'data' => '<!--',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!--',
                'state' => array(
                    0 => 'ScriptDataState',
                    1 => 'ScriptDataLessThanSignState',
                    2 => 'ScriptDataEscapeStartState',
                    3 => 'ScriptDataEscapeStartDashState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<script><!--<script ';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'StartTag',
                'data' => 'script',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '<script>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                ),
            ),
            1 => array(
                'type' => 'Character',
                'data' => '<!--<script ',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!--<script ',
                'state' => array(
                    0 => 'ScriptDataState',
                    1 => 'ScriptDataLessThanSignState',
                    2 => 'ScriptDataEscapeStartState',
                    3 => 'ScriptDataEscapeStartDashState',
                    4 => 'ScriptDataEscapedDashDashState',
                    5 => 'ScriptDataEscapedLessThanSignState',
                    6 => 'ScriptDataDoubleEscapeStartState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<script><!--<script -';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'StartTag',
                'data' => 'script',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '<script>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                ),
            ),
            1 => array(
                'type' => 'Character',
                'data' => '<!--<script -',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!--<script -',
                'state' => array(
                    0 => 'ScriptDataState',
                    1 => 'ScriptDataLessThanSignState',
                    2 => 'ScriptDataEscapeStartState',
                    3 => 'ScriptDataEscapeStartDashState',
                    4 => 'ScriptDataEscapedDashDashState',
                    5 => 'ScriptDataEscapedLessThanSignState',
                    6 => 'ScriptDataDoubleEscapeStartState',
                    12 => 'ScriptDataDoubleEscapedState',
                ),

            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<script><!--<script --';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'StartTag',
                'data' => 'script',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '<script>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                ),
            ),
            1 => array(
                'type' => 'Character',
                'data' => '<!--<script --',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!--<script --',
                'state' => array(
                    0 => 'ScriptDataState',
                    1 => 'ScriptDataLessThanSignState',
                    2 => 'ScriptDataEscapeStartState',
                    3 => 'ScriptDataEscapeStartDashState',
                    4 => 'ScriptDataEscapedDashDashState',
                    5 => 'ScriptDataEscapedLessThanSignState',
                    6 => 'ScriptDataDoubleEscapeStartState',
                    12 => 'ScriptDataDoubleEscapedState',
                    13 => 'ScriptDataDoubleEscapedDashState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<p ';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'EndOfFile',
                'data' => '<p ',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<p ',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<p i';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'EndOfFile',
                'data' => '<p i',
                'selfClosing' => false,
                'attributes' => array(
                    0 => array(
                        'name' => 'i',
                        'value' => '',
                        'quoted' => false,
                    ),
                ),
                'parseError' => true,
                'html' => '<p i',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                    3 => 'BeforeAttributeNameState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<p id ';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'EndOfFile',
                'data' => '<p id ',
                'selfClosing' => false,
                'attributes' => array(
                    0 => array(
                        'name' => 'id',
                        'value' => '',
                        'quoted' => false,
                    ),
                ),
                'parseError' => true,
                'html' => '<p id ',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                    3 => 'BeforeAttributeNameState',
                    4 => 'AttributeNameState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<p id=';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'EndOfFile',
                'data' => '<p id=',
                'selfClosing' => false,
                'attributes' => array(
                    0 => array(
                        'name' => 'id',
                        'value' => '',
                        'quoted' => false,
                    ),
                ),
                'parseError' => true,
                'html' => '<p id=',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                    3 => 'BeforeAttributeNameState',
                    4 => 'AttributeNameState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<p id="';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'EndOfFile',
                'data' => '<p id="',
                'selfClosing' => false,
                'attributes' => array(
                    0 => array(
                        'name' => 'id',
                        'value' => '',
                        'quoted' => false,
                    ),
                ),
                'parseError' => true,
                'html' => '<p id="',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                    3 => 'BeforeAttributeNameState',
                    4 => 'AttributeNameState',
                    6 => 'BeforeAttributeValueState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<p id=\'';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'EndOfFile',
                'data' => '<p id=\'',
                'selfClosing' => false,
                'attributes' => array(
                    0 => array(
                        'name' => 'id',
                        'value' => '',
                        'quoted' => false,
                    ),
                ),
                'parseError' => true,
                'html' => '<p id=\'',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                    3 => 'BeforeAttributeNameState',
                    4 => 'AttributeNameState',
                    6 => 'BeforeAttributeValueState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<p id="id"';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'EndOfFile',
                'data' => '<p id="id"',
                'selfClosing' => false,
                'attributes' => array(
                    0 => array(
                        'name' => 'id',
                        'value' => 'id',
                        'quoted' => '"',
                    ),
                ),
                'parseError' => true,
                'html' => '<p id="id"',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                    3 => 'BeforeAttributeNameState',
                    4 => 'AttributeNameState',
                    6 => 'BeforeAttributeValueState',
                    7 => 'AttributeValueDoubleQuotedState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<img id="id"/';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'EndOfFile',
                'data' => '<img id="id"/',
                'selfClosing' => false,
                'attributes' => array(
                    0 => array(
                        'name' => 'id',
                        'value' => 'id',
                        'quoted' => '"',
                    ),
                ),
                'parseError' => true,
                'html' => '<img id="id"/',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                    5 => 'BeforeAttributeNameState',
                    6 => 'AttributeNameState',
                    8 => 'BeforeAttributeValueState',
                    9 => 'AttributeValueDoubleQuotedState',
                    12 => 'AfterAttributeValueQuotedState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<!';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'Comment',
                'data' => '<!',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<!-';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'Uninitialized',
                'data' => '<!-',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '<!-',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<!--';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'Comment',
                'data' => '<!--',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!--',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<!-->';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'Comment',
                'data' => '<!-->',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!-->',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    4 => 'CommentStartState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<!---';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'Comment',
                'data' => '<!---',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!---',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    4 => 'CommentStartState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<!--->';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'Comment',
                'data' => '<!--->',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!--->',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    4 => 'CommentStartState',
                    5 => 'CommentStartDashState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<!----';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'Comment',
                'data' => '<!----',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!----',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    4 => 'CommentStartState',
                    5 => 'CommentStartDashState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<!----!';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'Comment',
                'data' => '<!----!',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!----!',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    4 => 'CommentStartState',
                    5 => 'CommentStartDashState',
                    6 => 'CommentEndState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<!DOCTYPE';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<!DOCTYPE ';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE ',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE ',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<!DOCTYPE P';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE P',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE P',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<!DOCTYPE P ';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE P ',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE P ',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<!DOCTYPE html PUBLIC';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE html PUBLIC',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE html PUBLIC',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<!DOCTYPE html PUBLIC ';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE html PUBLIC ',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE html PUBLIC ',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPEPublicKeywordState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<!DOCTYPE html PUBLIC "';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE html PUBLIC "',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE html PUBLIC "',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPEPublicKeywordState',
                    22 => 'BeforeDOCTYPEPublicIdentifierState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<!DOCTYPE html PUBLIC \'';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE html PUBLIC \'',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE html PUBLIC \'',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPEPublicKeywordState',
                    22 => 'BeforeDOCTYPEPublicIdentifierState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<!DOCTYPE html PUBLIC ""';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE html PUBLIC ""',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE html PUBLIC ""',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPEPublicKeywordState',
                    22 => 'BeforeDOCTYPEPublicIdentifierState',
                    23 => 'DOCTYPEPublicIdentifierDoubleQuotedState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<!DOCTYPE html PUBLIC "" ';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE html PUBLIC "" ',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE html PUBLIC "" ',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPEPublicKeywordState',
                    22 => 'BeforeDOCTYPEPublicIdentifierState',
                    23 => 'DOCTYPEPublicIdentifierDoubleQuotedState',
                    24 => 'AfterDOCTYPEPublicIdentifierState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<!DOCTYPE memo SYSTEM';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE memo SYSTEM',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE memo SYSTEM',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<!DOCTYPE memo SYSTEM ';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE memo SYSTEM ',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE memo SYSTEM ',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPESystemKeywordState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<!DOCTYPE memo SYSTEM "';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE memo SYSTEM "',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE memo SYSTEM "',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPESystemKeywordState',
                    22 => 'BeforeDOCTYPESystemIdentifierState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<!DOCTYPE memo SYSTEM \'';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE memo SYSTEM \'',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE memo SYSTEM \'',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPESystemKeywordState',
                    22 => 'BeforeDOCTYPESystemIdentifierState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<!DOCTYPE memo SYSTEM ""';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE memo SYSTEM ""',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE memo SYSTEM ""',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPESystemKeywordState',
                    22 => 'BeforeDOCTYPESystemIdentifierState',
                    23 => 'DOCTYPESystemIdentifierDoubleQuotedState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<!DOCTYPE memo SYSTEM "http://www.4dd.co.jp/DTD/memo.dtd"';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE memo SYSTEM "http://www.4dd.co.jp/DTD/memo.dtd"',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE memo SYSTEM "http://www.4dd.co.jp/DTD/memo.dtd"',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPESystemKeywordState',
                    22 => 'BeforeDOCTYPESystemIdentifierState',
                    23 => 'DOCTYPESystemIdentifierDoubleQuotedState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<!DOCTYPE memo D"';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE memo D"',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE memo D"',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    16 => 'BogusDOCTYPEState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<![CDATA[';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'EndOfFile',
                'data' => '<![CDATA[',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '<![CDATA[',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<a';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'EndOfFile',
                'data' => '<a',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<a',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        $html = $source = '<!-- -';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'Comment',
                'data' => '<!-- -',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!-- -',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    4 => 'CommentStartState',
                    5 => 'CommentState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);
    }

    public function testParseError() {
        // TagOpenState
        $html = $source = '<<?';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'Character',
                'data' => '<',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<',
                'state' => array(
                    0 => 'DataState',
                ),
            ),
            1 => array(
                'type' => 'Comment',
                'data' => '<?',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<?',
                'state' => array(
                    0 => 'DataState',
                    1 => 'ContinueBogusCommentState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // TagOpenState
        $html = $source = '<<?';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'Character',
                'data' => '<',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<',
                'state' => array(
                    0 => 'DataState',
                ),
            ),
            1 => array(
                'type' => 'Comment',
                'data' => '<?',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<?',
                'state' => array(
                    0 => 'DataState',
                    1 => 'ContinueBogusCommentState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // EndTagOpenState
        $html = $source = '</>';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'EndOfFile',
                'data' => '</>',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '</>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'EndTagOpenState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // EndTagOpenState
        $html = $source = '<//';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'Comment',
                'data' => '<//',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<//',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'ContinueBogusCommentState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // AfterAttributeNameState
        $html = $source = '<IMG SRC "="img.png">';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'StartTag',
                'data' => 'img',
                'selfClosing' => false,
                'attributes' => array(
                    0 => array(
                        'name' => 'src',
                        'value' => '',
                        'quoted' => false,
                    ),
                    1 => array(
                        'name' => '"',
                        'value' => 'img.png',
                        'quoted' => '"',
                    ),
                ),
                'parseError' => true,
                'html' => '<IMG SRC "="img.png">',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                    5 => 'BeforeAttributeNameState',
                    6 => 'AttributeNameState',
                    9 => 'AfterAttributeNameState',
                    10 => 'AttributeNameState',
                    11 => 'BeforeAttributeValueState',
                    12 => 'AttributeValueDoubleQuotedState',
                    20 => 'AfterAttributeValueQuotedState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // BeforeAttributeValueState
        $html = $source = '<IMG SRC=>';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'StartTag',
                'data' => 'img',
                'selfClosing' => false,
                'attributes' => array(
                    0 => array(
                        'name' => 'src',
                        'value' => '',
                        'quoted' => false,
                    ),
                ),
                'parseError' => true,
                'html' => '<IMG SRC=>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                    5 => 'BeforeAttributeNameState',
                    6 => 'AttributeNameState',
                    9 => 'BeforeAttributeValueState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // BeforeAttributeValueState
        $html = $source = '<IMG SRC=<"';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'EndOfFile',
                'data' => '<IMG SRC=<"',
                'selfClosing' => false,
                'attributes' => array(
                    0 => array(
                        'name' => 'src',
                        'value' => '<"',
                        'quoted' => false,
                    ),
                ),
                'parseError' => true,
                'html' => '<IMG SRC=<"',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                    5 => 'BeforeAttributeNameState',
                    6 => 'AttributeNameState',
                    9 => 'BeforeAttributeValueState',
                    10 => 'AttributeValueUnquotedState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // AfterAttributeValueQuotedState
        $html = $source = '<IMG SRC="""';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'EndOfFile',
                'data' => '<IMG SRC="""',
                'selfClosing' => false,
                'attributes' => array(
                    0 => array(
                        'name' => 'src',
                        'value' => '',
                        'quoted' => '"',
                    ),
                    1 => array(
                        'name' => '"',
                        'value' => '',
                        'quoted' => false,
                    ),
                ),
                'parseError' => true,
                'html' => '<IMG SRC="""',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                    5 => 'BeforeAttributeNameState',
                    6 => 'AttributeNameState',
                    9 => 'BeforeAttributeValueState',
                    10 => 'AttributeValueDoubleQuotedState',
                    11 => 'BeforeAttributeNameState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // SelfClosingStartTagState
        $html = $source = '<IMG SRC=""/ ';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'EndOfFile',
                'data' => '<IMG SRC=""/ ',
                'selfClosing' => false,
                'attributes' => array(
                    0 => array(
                        'name' => 'src',
                        'value' => '',
                        'quoted' => '"',
                    ),
                ),
                'parseError' => true,
                'html' => '<IMG SRC=""/ ',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                    5 => 'BeforeAttributeNameState',
                    6 => 'AttributeNameState',
                    9 => 'BeforeAttributeValueState',
                    10 => 'AttributeValueDoubleQuotedState',
                    11 => 'AfterAttributeValueQuotedState',
                    12 => 'BeforeAttributeNameState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // CommentEndState
        $html = $source = '<!-- ---';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'Comment',
                'data' => '<!-- ---',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!-- ---',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    4 => 'CommentStartState',
                    5 => 'CommentState',
                    6 => 'CommentEndDashState',
                    7 => 'CommentEndState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // CommentEndState
        $html = $source = '<!-- --<';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'Comment',
                'data' => '<!-- --<',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!-- --<',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    4 => 'CommentStartState',
                    5 => 'CommentState',
                    6 => 'CommentEndDashState',
                    7 => 'CommentEndState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // DOCTYPEState
        $html = $source = '<!DOCTYPE_';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE_',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE_',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'BeforeDOCTYPENameState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // BeforeDOCTYPENameState
        $html = $source = '<!DOCTYPE >';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE >',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE >',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // BeforeDOCTYPENameState
        $html = $source = '<!DOCTYPE HTML PUBLIC"';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE HTML PUBLIC"',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE HTML PUBLIC"',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPEPublicKeywordState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // BeforeDOCTYPENameState
        $html = $source = '<!DOCTYPE HTML PUBLIC\'';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE HTML PUBLIC\'',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE HTML PUBLIC\'',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPEPublicKeywordState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // BeforeDOCTYPENameState
        $html = $source = '<!DOCTYPE HTML PUBLIC>';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE HTML PUBLIC>',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE HTML PUBLIC>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPEPublicKeywordState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // BeforeDOCTYPENameState
        $html = $source = '<!DOCTYPE HTML PUBLIC_';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE HTML PUBLIC_',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE HTML PUBLIC_',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPEPublicKeywordState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // BeforeDOCTYPEPublicIdentifierState
        $html = $source = '<!DOCTYPE HTML PUBLIC >';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE HTML PUBLIC >',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE HTML PUBLIC >',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPEPublicKeywordState',
                    22 => 'BeforeDOCTYPEPublicIdentifierState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // BeforeDOCTYPEPublicIdentifierState
        $html = $source = '<!DOCTYPE HTML PUBLIC _';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE HTML PUBLIC _',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE HTML PUBLIC _',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPEPublicKeywordState',
                    22 => 'BeforeDOCTYPEPublicIdentifierState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // DOCTYPEPublicIdentifierDoubleQuotedState
        $html = $source = '<!DOCTYPE HTML PUBLIC ">';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE HTML PUBLIC ">',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE HTML PUBLIC ">',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPEPublicKeywordState',
                    22 => 'BeforeDOCTYPEPublicIdentifierState',
                    23 => 'DOCTYPEPublicIdentifierDoubleQuotedState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // DOCTYPEPublicIdentifierSingleQuotedState
        $html = $source = '<!DOCTYPE HTML PUBLIC \'>';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE HTML PUBLIC \'>',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE HTML PUBLIC \'>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPEPublicKeywordState',
                    22 => 'BeforeDOCTYPEPublicIdentifierState',
                    23 => 'DOCTYPEPublicIdentifierSingleQuotedState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // AfterDOCTYPEPublicIdentifierState
        $html = $source = '<!DOCTYPE html PUBLIC """';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE html PUBLIC """',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE html PUBLIC """',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPEPublicKeywordState',
                    22 => 'BeforeDOCTYPEPublicIdentifierState',
                    23 => 'DOCTYPEPublicIdentifierDoubleQuotedState',
                    24 => 'AfterDOCTYPEPublicIdentifierState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // AfterDOCTYPEPublicIdentifierState
        $html = $source = '<!DOCTYPE html PUBLIC ""\'';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE html PUBLIC ""\'',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE html PUBLIC ""\'',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPEPublicKeywordState',
                    22 => 'BeforeDOCTYPEPublicIdentifierState',
                    23 => 'DOCTYPEPublicIdentifierDoubleQuotedState',
                    24 => 'AfterDOCTYPEPublicIdentifierState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // AfterDOCTYPEPublicIdentifierState
        $html = $source = '<!DOCTYPE html PUBLIC ""-';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE html PUBLIC ""-',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE html PUBLIC ""-',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPEPublicKeywordState',
                    22 => 'BeforeDOCTYPEPublicIdentifierState',
                    23 => 'DOCTYPEPublicIdentifierDoubleQuotedState',
                    24 => 'AfterDOCTYPEPublicIdentifierState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // BetweenDOCTYPEPublicAndSystemIdentifiersState
        $html = $source = '<!DOCTYPE html PUBLIC "" -';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE html PUBLIC "" -',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE html PUBLIC "" -',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPEPublicKeywordState',
                    22 => 'BeforeDOCTYPEPublicIdentifierState',
                    23 => 'DOCTYPEPublicIdentifierDoubleQuotedState',
                    24 => 'AfterDOCTYPEPublicIdentifierState',
                    25 => 'BetweenDOCTYPEPublicAndSystemIdentifiersState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // AfterDOCTYPESystemKeywordState
        $html = $source = '<!DOCTYPE html SYSTEM"';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE html SYSTEM"',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE html SYSTEM"',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPESystemKeywordState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // AfterDOCTYPESystemKeywordState
        $html = $source = '<!DOCTYPE html SYSTEM\'';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE html SYSTEM\'',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE html SYSTEM\'',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPESystemKeywordState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // AfterDOCTYPESystemKeywordState
        $html = $source = '<!DOCTYPE html SYSTEM>';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE html SYSTEM>',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE html SYSTEM>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPESystemKeywordState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // AfterDOCTYPESystemKeywordState
        $html = $source = '<!DOCTYPE html SYSTEM-';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE html SYSTEM-',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE html SYSTEM-',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPESystemKeywordState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // BeforeDOCTYPESystemIdentifierState
        $html = $source = '<!DOCTYPE html SYSTEM >';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE html SYSTEM >',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE html SYSTEM >',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPESystemKeywordState',
                    22 => 'BeforeDOCTYPESystemIdentifierState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // BeforeDOCTYPESystemIdentifierState
        $html = $source = '<!DOCTYPE html SYSTEM -';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE html SYSTEM -',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE html SYSTEM -',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPESystemKeywordState',
                    22 => 'BeforeDOCTYPESystemIdentifierState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // DOCTYPESystemIdentifierDoubleQuotedState
        $html = $source = '<!DOCTYPE html SYSTEM ">';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE html SYSTEM ">',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE html SYSTEM ">',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPESystemKeywordState',
                    22 => 'BeforeDOCTYPESystemIdentifierState',
                    23 => 'DOCTYPESystemIdentifierDoubleQuotedState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // DOCTYPESystemIdentifierDoubleQuotedState
        $html = $source = '<!DOCTYPE html SYSTEM \'>';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE html SYSTEM \'>',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE html SYSTEM \'>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPESystemKeywordState',
                    22 => 'BeforeDOCTYPESystemIdentifierState',
                    23 => 'DOCTYPESystemIdentifierSingleQuotedState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);

        // AfterDOCTYPESystemIdentifierState
        $html = $source = '<!DOCTYPE html SYSTEM ""-';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'DOCTYPE',
                'data' => '<!DOCTYPE html SYSTEM ""-',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => true,
                'html' => '<!DOCTYPE html SYSTEM ""-',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'MarkupDeclarationOpenState',
                    9 => 'DOCTYPEState',
                    10 => 'BeforeDOCTYPENameState',
                    11 => 'DOCTYPENameState',
                    15 => 'AfterDOCTYPENameState',
                    21 => 'AfterDOCTYPESystemKeywordState',
                    22 => 'BeforeDOCTYPESystemIdentifierState',
                    23 => 'DOCTYPESystemIdentifierDoubleQuotedState',
                    24 => 'AfterDOCTYPESystemIdentifierState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);
    }

    public function testRAWTEXTState() {
        $html = $source = '<style>  /**/  </style>';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'StartTag',
                'data' => 'style',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '<style>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                ),
            ),
            1 => array(
                'type' => 'Character',
                'data' => '  /**/  ',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '  /**/  ',
                'state' => array(
                    0 => 'RAWTEXTState',
                ),
            ),
            2 => array(
                'type' => 'EndTag',
                'data' => 'style',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '</style>',
                'state' => array(
                    0 => 'RAWTEXTLessThanSignState',
                    1 => 'RAWTEXTEndTagOpenState',
                    2 => 'RAWTEXTEndTagNameState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);
    }

    public function testScriptDataState() {
        $html = $source = '<script>  /**/  </script>';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'StartTag',
                'data' => 'script',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '<script>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                ),
            ),
            1 => array(
                'type' => 'Character',
                'data' => '  /**/  ',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '  /**/  ',
                'state' => array(
                    0 => 'ScriptDataState',
                ),
            ),
            2 => array(
                'type' => 'EndTag',
                'data' => 'script',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '</script>',
                'state' => array(
                    0 => 'ScriptDataLessThanSignState',
                    1 => 'ScriptDataEndTagOpenState',
                    2 => 'ScriptDataEndTagNameState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);
    }

    public function testRCDATAState() {
        $html = $source = '<title>  /**/  </title>';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'StartTag',
                'data' => 'title',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '<title>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                ),
            ),
            1 => array(
                'type' => 'Character',
                'data' => '  /**/  ',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '  /**/  ',
                'state' => array(
                    0 => 'RCDATAState',
                ),
            ),
            2 => array(
                'type' => 'EndTag',
                'data' => 'title',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '</title>',
                'state' => array(
                    0 => 'RCDATALessThanSignState',
                    1 => 'RCDATAEndTagOpenState',
                    2 => 'RCDATAEndTagNameState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);
    }

    public function testNestDataState() {
        $html = $source = '<textarea><script> var Hello = \'world\';</script></textarea>';
        $SegmentedString = new SegmentedString($html);
        $HTMLTokenizer = new HTMLTokenizer($SegmentedString, array('debug' => true));
        $HTMLTokenizer->tokenizer();
        $actual = $HTMLTokenizer->getTokensAsArray();
        $expect = array(
            0 => array(
                'type' => 'StartTag',
                'data' => 'textarea',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '<textarea>',
                'state' => array(
                    0 => 'DataState',
                    1 => 'TagOpenState',
                    2 => 'TagNameState',
                ),
            ),
            1 => array(
                'type' => 'Character',
                'data' => '<script> var Hello = \'world\';</script>',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '<script> var Hello = \'world\';</script>',
                'state' => array(
                    0 => 'RCDATAState',
                ),
            ),
            2 => array(
                'type' => 'EndTag',
                'data' => 'textarea',
                'selfClosing' => false,
                'attributes' => array(),
                'parseError' => false,
                'html' => '</textarea>',
                'state' => array(
                    0 => 'RCDATALessThanSignState',
                    1 => 'RCDATAEndTagOpenState',
                    2 => 'RCDATAEndTagNameState',
                ),
            ),
        );
        $this->assertEquals($expect, $actual);
    }
}