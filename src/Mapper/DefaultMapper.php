<?php

namespace Mailery\User\Mapper;

use Mailery\Activity\Log\Mapper\LoggableMapper;
use Mailery\User\Module;

/**
 * @Cycle\Annotated\Annotation\Table(
 *      columns = {
 *          "created_at": @Cycle\Annotated\Annotation\Column(type = "datetime"),
 *          "updated_at": @Cycle\Annotated\Annotation\Column(type = "datetime")
 *      }
 * )
 */
class DefaultMapper extends LoggableMapper
{
    /**
     * {@inheritdoc}
     */
    protected function getModule(): string
    {
        return Module::NAME;
    }
}