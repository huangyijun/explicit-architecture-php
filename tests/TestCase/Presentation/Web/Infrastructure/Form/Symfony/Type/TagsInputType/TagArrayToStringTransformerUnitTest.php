<?php

declare(strict_types=1);

/*
 * This file is part of the Explicit Architecture POC,
 * which is created on top of the Symfony Demo application.
 *
 * (c) Herberto Graça <herberto.graca@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Acme\App\Test\TestCase\Presentation\Web\Infrastructure\Form\Symfony\Type\TagsInputType;

use Acme\App\Core\Component\Blog\Domain\Post\Tag\Tag;
use Acme\App\Presentation\Web\Infrastructure\Form\Symfony\Type\TagsInputType\TagArrayToStringTransformer;
use Acme\App\Test\Framework\AbstractUnitTest;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;

/**
 * Tests that tags are transformed correctly using the data transformer.
 *
 * See https://symfony.com/doc/current/testing/database.html
 *
 * @small
 *
 * @internal
 */
final class TagArrayToStringTransformerUnitTest extends AbstractUnitTest
{
    /**
     * @test
     *
     * Ensures that tags are created correctly.
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function create_the_right_amount_of_tags(): void
    {
        $tags = $this->getMockedTransformer()->reverseTransform('Hello, Demo, How');

        self::assertCount(3, $tags);
        self::assertSame('Hello', (string) $tags[0]);
    }

    /**
     * @test
     *
     * Ensures that empty tags and errors in the number of commas are dealt correctly.
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function create_the_right_amount_of_tags_with_too_many_commas(): void
    {
        $transformer = $this->getMockedTransformer();

        self::assertCount(3, $transformer->reverseTransform('Hello, Demo,, How'));
        self::assertCount(3, $transformer->reverseTransform('Hello, Demo, How,'));
    }

    /**
     * @test
     *
     * Ensures that leading/trailing spaces are ignored for tag names.
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function trim_names(): void
    {
        $tags = $this->getMockedTransformer()->reverseTransform('   Hello   ');

        self::assertSame('Hello', (string) $tags[0]);
    }

    /**
     * @test
     *
     * Ensures that duplicated tag names are ignored.
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function duplicate_names(): void
    {
        $tags = $this->getMockedTransformer()->reverseTransform('Hello, Hello, Hello');

        self::assertCount(1, $tags);
    }

    /**
     * @test
     *
     * Ensures that the transformer uses tags already persisted in the database.
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function uses_already_defined_tags(): void
    {
        $persistedTags = [
            new Tag('Hello'),
            new Tag('World'),
        ];
        $tags = $this->getMockedTransformer($persistedTags)->reverseTransform('Hello, World, How, Are, You');

        self::assertCount(5, $tags);
        self::assertSame($persistedTags[0], $tags[0]);
        self::assertSame($persistedTags[1], $tags[1]);
    }

    /**
     * @test
     *
     * Ensures that the transformation from Tag instances to a simple string
     * works as expected.
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function transform(): void
    {
        $persistedTags = [
            new Tag('Hello'),
            new Tag('World'),
        ];
        $transformed = $this->getMockedTransformer()->transform($persistedTags);

        self::assertSame('Hello,World', $transformed);
    }

    /**
     * This helper method mocks the real TagArrayToStringTransformer class to
     * simplify the tests. See https://phpunit.de/manual/current/en/test-doubles.html.
     *
     * @param array $findByReturnValues The values returned when calling to the findBy() method
     *
     * @throws \Doctrine\ORM\ORMException
     */
    private function getMockedTransformer(array $findByReturnValues = []): TagArrayToStringTransformer
    {
        $tagRepository = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $tagRepository->expects(self::any())
            ->method('findBy')
            ->willReturn($findByReturnValues);

        $entityManager = $this
            ->getMockBuilder(ObjectManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $entityManager->expects(self::any())
            ->method('getRepository')
            ->willReturn($tagRepository);

        return new TagArrayToStringTransformer($entityManager);
    }
}
