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

namespace Acme\App\Core\Component\Blog\Application\Repository;

use Acme\App\Core\Component\Blog\Domain\Entity\Comment;
use Acme\App\Core\Component\Blog\Domain\Entity\CommentId;

/**
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
interface CommentRepositoryInterface
{
    public function find(CommentId $id): Comment;

    public function upsert(Comment $entity): void;
}
