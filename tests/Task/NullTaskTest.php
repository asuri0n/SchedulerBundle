<?php

declare(strict_types=1);

namespace Tests\SchedulerBundle\Task;

use DateTimeImmutable;
use Generator;
use PHPUnit\Framework\TestCase;
use SchedulerBundle\Exception\InvalidArgumentException;
use SchedulerBundle\Exception\LogicException;
use SchedulerBundle\Task\NullTask;
use SchedulerBundle\Task\ShellTask;
use SchedulerBundle\Task\TaskInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use function sprintf;

/**
 * @author Guillaume Loulier <contact@guillaumeloulier.fr>
 */
final class NullTaskTest extends TestCase
{
    public function testTaskCanBeCreatedWithValidInformations(): void
    {
        $task = new NullTask('foo', [
            'expression' => '* * * * *',
            'background' => true,
            'state' => TaskInterface::DISABLED,
        ]);

        self::assertSame('foo', $task->getName());
        self::assertSame('* * * * *', $task->getExpression());
        self::assertTrue($task->mustRunInBackground());
        self::assertNull($task->getDescription());
        self::assertSame(TaskInterface::DISABLED, $task->getState());
    }

    public function testTaskCannotBeCreatedWithInvalidArrivalTime(): void
    {
        self::expectException(InvalidOptionsException::class);
        self::expectExceptionMessage('The option "arrival_time" with value 153 is expected to be of type "DateTimeImmutable" or "null", but is of type "int"');
        self::expectExceptionCode(0);
        new NullTask('foo', [
            'arrival_time' => 153,
        ]);
    }

    public function testTaskCannotBeCreatedWithInvalidBackground(): void
    {
        self::expectException(InvalidOptionsException::class);
        self::expectExceptionMessage('The option "background" with value 153 is expected to be of type "bool", but is of type "int"');
        self::expectExceptionCode(0);
        new NullTask('foo', [
            'background' => 153,
        ]);
    }

    public function testTaskCannotBeCreatedWithInvalidBeforeScheduling(): void
    {
        self::expectException(InvalidOptionsException::class);
        self::expectExceptionMessage('The option "before_scheduling" with value "foo" is expected to be of type "callable" or "array" or "null", but is of type "string"');
        self::expectExceptionCode(0);
        new NullTask('foo', [
            'before_scheduling' => 'foo',
        ]);
    }

    public function testTaskCannotBeCreatedWithInvalidAfterScheduling(): void
    {
        self::expectException(InvalidOptionsException::class);
        self::expectExceptionMessage('The option "after_scheduling" with value "foo" is expected to be of type "callable" or "array" or "null", but is of type "string"');
        self::expectExceptionCode(0);
        new NullTask('foo', [
            'after_scheduling' => 'foo',
        ]);
    }

    public function testTaskCannotBeCreatedWithInvalidBeforeExecuting(): void
    {
        self::expectException(InvalidOptionsException::class);
        self::expectExceptionMessage('The option "before_executing" with value "foo" is expected to be of type "callable" or "array" or "null", but is of type "string"');
        self::expectExceptionCode(0);
        new NullTask('foo', [
            'before_executing' => 'foo',
        ]);
    }

    public function testTaskCannotBeCreatedWithInvalidAfterExecuting(): void
    {
        self::expectException(InvalidOptionsException::class);
        self::expectExceptionMessage('The option "after_executing" with value "foo" is expected to be of type "callable" or "array" or "null", but is of type "string"');
        self::expectExceptionCode(0);
        new NullTask('foo', [
            'after_executing' => 'foo',
        ]);
    }

    public function testTaskCannotBeCreatedWithInvalidDescription(): void
    {
        self::expectException(InvalidOptionsException::class);
        self::expectExceptionMessage('The option "description" with value 354 is expected to be of type "string" or "null", but is of type "int"');
        self::expectExceptionCode(0);
        new NullTask('foo', [
            'description' => 354,
        ]);
    }

    public function testTaskCannotBeCreatedWithInvalidExpression(): void
    {
        self::expectException(InvalidOptionsException::class);
        self::expectExceptionMessage('The option "expression" with value 354 is expected to be of type "string", but is of type "int"');
        self::expectExceptionCode(0);
        new NullTask('foo', [
            'expression' => 354,
        ]);
    }

    public function testTaskCannotBeCreatedWithInvalidPriority(): void
    {
        self::expectException(InvalidOptionsException::class);
        self::expectExceptionMessage('The option "priority" with value "foo" is expected to be of type "int", but is of type "string"');
        self::expectExceptionCode(0);
        new NullTask('foo', [
            'priority' => 'foo',
        ]);
    }

    public function testTaskCannotBeCreatedWithInvalidBackgroundOption(): void
    {
        $task = new NullTask('foo');

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage(sprintf('The background option is available only for task of type %s', ShellTask::class));
        self::expectExceptionCode(0);
        $task->setBackground(true);
    }

    /**
     * @dataProvider provideNice
     */
    public function testTaskCannotBeCreatedWithInvalidNice(int $nice): void
    {
        $task = new NullTask('foo');

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('The nice value is not valid');
        self::expectExceptionCode(0);
        $task->setNice($nice);
    }

    public function testTaskCannotBeCreatedWithPreviousDate(): void
    {
        $task = new NullTask('foo');

        self::expectException(LogicException::class);
        self::expectExceptionMessage('The date cannot be previous to the current date');
        self::expectExceptionCode(0);
        $task->setExecutionStartDate('- 10 minutes');
    }

    public function testTaskCanBeCreatedWithDate(): void
    {
        $task = new NullTask('foo');
        $task->setExecutionStartDate('+ 10 minutes');
        $task->setExecutionEndDate('+ 20 minutes');

        self::assertInstanceOf(DateTimeImmutable::class, $task->getExecutionStartDate());
        self::assertInstanceOf(DateTimeImmutable::class, $task->getExecutionEndDate());
    }

    public function provideNice(): Generator
    {
        yield [20];
        yield [-25];
        yield [200];
    }
}
