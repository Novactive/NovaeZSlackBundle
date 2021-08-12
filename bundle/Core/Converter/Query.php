<?php

/**
 * NovaeZSlackBundle Bundle.
 *
 * @package   Novactive\Bundle\eZSlackBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZSlackBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZSlackBundle\Core\Converter;

use Carbon\Carbon;
use eZ\Publish\API\Repository\Values\Content\Query as eZQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use QueryTranslator\Languages\Galach\Parser;
use QueryTranslator\Languages\Galach\TokenExtractor\Full as FullTokenExtractor;
use QueryTranslator\Languages\Galach\Tokenizer;
use QueryTranslator\Languages\Galach\Values\Node as VNode;
use QueryTranslator\Languages\Galach\Values\Token as VToken;
use QueryTranslator\Values\Node;
use RuntimeException;

/**
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class Query
{
    public function convert(string $searchQuery, int $offset = 0, int $limit = 20): eZQuery
    {
        $tokenExtractor = new FullTokenExtractor();
        $tokenizer = new Tokenizer($tokenExtractor);
        $tokenSequence = $tokenizer->tokenize($searchQuery);
        $parser = new Parser();
        $syntaxTree = $parser->parse($tokenSequence);
        $query = new eZQuery();
        $query->filter = $this->build($syntaxTree->rootNode);
        $query->offset = $offset;
        $query->limit = $limit;

        return $query;
    }

    private function build(Node $node): ?Criterion
    {
        if ($node instanceof Vnode\LogicalAnd) {
            $criterion = new Criterion\LogicalAnd(
                [
                    $this->build($node->leftOperand),
                    $this->build($node->rightOperand),
                ]
            );

            return $criterion;
        }

        if ($node instanceof Vnode\LogicalOr) {
            $criterion = new Criterion\LogicalOr(
                [
                    $this->build($node->leftOperand),
                    $this->build($node->rightOperand),
                ]
            );

            return $criterion;
        }

        if ($node instanceof Vnode\LogicalNot) {
            return new Criterion\LogicalNot($this->build($node->operand));
        }
        if ($node instanceof Vnode\Mandatory) {
            return $this->build($node->operand);
        }
        if ($node instanceof Vnode\Prohibited) {
            return new Criterion\LogicalNot($this->build($node->operand));
        }

        if ($node instanceof Vnode\Group) {
            if (\count($node->getNodes())) {
                $criterions = [];
                foreach ($node->getNodes() as $subNode) {
                    $criterions[] = $this->build($subNode);
                }

                return new Criterion\LogicalOr($criterions);
            }
        }

        if ($node instanceof Vnode\Term) {
            return $this->getTermCriterion($node);
        }

        if ($node instanceof Vnode\Query) {
            if (\count($node->getNodes())) {
                $criterions = [];
                foreach ($node->getNodes() as $subNode) {
                    $criterions[] = $this->build($subNode);
                }

                return new Criterion\LogicalOr($criterions);
            }
        }

        return null;
    }

    private function getDatedCriterion(string $target, string $date): Criterion
    {
        $operator = Criterion\Operator::EQ;
        if (\in_array($date[0], [Criterion\Operator::GT, Criterion\Operator::LT])) {
            $operator = $date[0];
            $date = (string) substr($date, 1);
        }

        return new Criterion\DateMetadata($target, $operator, (new Carbon($date))->getTimestamp());
    }

    private function getTermCriterion(Vnode\Term $term): Criterion
    {
        if ($term->token instanceof VToken\Word) {
            if ('' === $term->token->domain) {
                return new Criterion\FullText($term->token->word);
            }
            if ('id' === strtolower($term->token->domain)) {
                return new Criterion\ContentId(explode('|', $term->token->word));
            }
            if ('lang' === strtolower($term->token->domain)) {
                return new Criterion\LanguageCode(explode('|', $term->token->word));
            }
            if ('section' === strtolower($term->token->domain)) {
                return new Criterion\SectionId(explode('|', $term->token->word));
            }
            if ('contenttype' === strtolower($term->token->domain)) {
                return new Criterion\ContentTypeIdentifier(explode('|', $term->token->word));
            }
            if (\in_array(strtolower($term->token->domain), ['published', 'created'])) {
                return $this->getDatedCriterion(Criterion\DateMetadata::CREATED, $term->token->word);
            }
            if (\in_array(strtolower($term->token->domain), ['modified', 'updated'])) {
                return $this->getDatedCriterion(Criterion\DateMetadata::MODIFIED, $term->token->word);
            }
        }
        if ($term->token instanceof VToken\Phrase) {
            // we don't manage the domain here
            return new Criterion\FullText($term->token->quote.$term->token->phrase.$term->token->quote);
        }
        if ($term->token instanceof VToken\Tag) {
            //@todo: treat them like real tag, eZ Tag?
            return new Criterion\FullText($term->token->tag);
        }

        throw new RuntimeException('Term '.\get_class($term).' not yet managed, consider doing a PR ;-)');
    }
}
