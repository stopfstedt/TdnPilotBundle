<?php

namespace Tdn\SfProjectGeneratorBundle\Model;

use Symfony\Component\Finder\SplFileInfo;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\PhpManipulator\TokenStream;
use JMS\PhpManipulator\TokenStream\AbstractToken;
use JMS\PhpManipulator\TokenStream\PhpToken;

/**
 * Class Source
 * @package Tdn\SfProjectGeneratorBundle\Model
 */
class Source
{
    /**
     * @var SplFileInfo
     */
    protected $file;

    /**
     * @var TokenStream
     */
    protected $stream;

    /**
     * @var \JMS\PhpManipulator\AstStream
     */
    protected $ast;

    /**
     * @param SplFileInfo $file
     * @param TokenStream $stream
     */
    public function __construct(SplFileInfo $file, TokenStream $stream)
    {
        $this->file = $file;
        $this->stream = $stream;
        $this->stream->setIgnoreComments(false);
        $this->stream->setCode($file->getContents());
    }

    /**
     * @param TokenStream $stream
     */
    public function setStream(TokenStream $stream)
    {
        $this->stream = $stream;
    }

    /**
     * @return TokenStream
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * @param SplFileInfo $file
     */
    public function setFile(SplFileInfo $file)
    {
        $this->file = $file;
    }

    /**
     * @return SplFileInfo
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param bool $withLines
     *
     * @return ArrayCollection
     */
    public function getInternalTokens($withLines = false)
    {
        $tokens = new ArrayCollection(array_map(
            function (AbstractToken $token) use ($withLines) {
                if ($token instanceof PhpToken) {
                    $rToken = token_name($token->getType());
                    return ($withLines) ?
                        new ArrayCollection(['token' => $rToken, 'line' => $token->getLine()]) : $rToken;
                }
            },
            $this->stream->getTokens()
        ));

        return $tokens;
    }

    /**
     * @param bool $withLines
     * @param bool $withAttributes
     *
     * @return ArrayCollection
     */
    public function getTokens($withLines = false, $withAttributes = false)
    {
        $tokens = new ArrayCollection(array_map(
            function (AbstractToken $token) use ($withLines, $withAttributes) {
                $rToken['token']      = $token->getContent();
                if ($withLines) {
                    $rToken['line'] = $token->getLine();
                }
                if ($withAttributes) {
                    $rToken['attributes'] = $token->getAttributes();
                }

                return $rToken;
            },
            $this->stream->getTokens()
        ));

        return $tokens;
    }

    public function __destruct()
    {
        $this->file = null;
    }
}