<?php
declare(strict_types=1);

namespace Sis\Stats\Test\TestCase\Model\Behavior;

use Cake\TestSuite\TestCase;

/**
 * Sis\Stats\Model\Behavior\StatsBehavior Test Case
 */
class StatsBehaviorTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Sis\Stats\Model\Behavior\StatsBehavior
     */
    public $StatsBehavior;

    /**
     * @var \Sis\Orgs\Model\Table\HostsTable
     */
    public $Hosts;

    /**
     * Defines which fixtures we'll be using.
     *
     * @return array<string>
     */
    public function getFixtures(): array
    {
        return [
            'plugin.Sis/Orgs.Ics',
            'plugin.Sis/Orgs.Hosts',
            'plugin.Sis/Stats.StatsCounts',
            'plugin.Sis/Stats.StatsEntities',
        ];
    }

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        /** @var \Cake\ORM\Locator\TableLocator $Locator */
        $Locator = $this->getTableLocator();
        $Locator->allowFallbackClass(false);

        /** @var \Sis\Orgs\Model\Table\HostsTable $Hosts */
        $Hosts = $Locator->get('Sis/Orgs.Hosts');
        $this->Hosts = $Hosts;

        /** @var \Sis\Stats\Model\Behavior\StatsBehavior $StatsBehavior */
        $StatsBehavior = $this->Hosts->getBehavior('Stats');
        $this->StatsBehavior = $StatsBehavior;
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization(): void
    {
        $this->assertInstanceOf(\Sis\Stats\Model\Table\StatsCountsTable::class, $this->StatsBehavior->StatsCounts);
        $this->assertInstanceOf(\Sis\Stats\Model\Table\StatsEntitiesTable::class, $this->StatsBehavior->StatsEntities);

        $this->assertSame([], $this->StatsBehavior->entities);

        $date = new \Cake\I18n\FrozenTime(new \DateTimeImmutable('2019-06-20'));

        $this->StatsBehavior->initialize([
            'entities' => [
                'Test.New' => [
                    'count' => 1,
                    'timestamp' => $date,
                    'increment' => true,
                    'timeperiods' => ['year', 'month', 'week', 'day', 'hour'],
                ],
                'PenTest.Results.1.open' => [],
            ],
        ]);

        $results = $this->StatsBehavior->statsGetByKey('Test.New');

        $this->assertInstanceOf(\Sis\Stats\Model\Entity\StatsEntity::class, $results);
        $this->assertSame(12, $results->get('id'));
        $this->assertSame('Test.New', $results->get('key'));
        $this->assertSame('Test New', $results->get('name'));
    }

    /**
     * Test statsGetByKey
     */
    public function testStatsGetByKey(): void
    {
        $results = $this->StatsBehavior->statsGetByKey('PenTest.Results.open');

        $this->assertInstanceOf(\Sis\Stats\Model\Entity\StatsEntity::class, $results);
        $this->assertSame(1, $results->get('id'));
        $this->assertSame('PenTest.Results.open', $results->get('key'));
        $this->assertSame('Open', $results->get('name'));
    }

    /**
     * Test statsTimeperiodFormats
     */
    public function testStatsTimeperiodFormats(): void
    {
        $this->assertNull($this->StatsBehavior->statsTimeperiodFormats('minute'));

        $results = $this->StatsBehavior->statsTimeperiodFormats('hour');

        $expected = 'yyyyMMddHH';

        $this->assertSame($expected, $results);

        $this->assertNull($this->StatsBehavior->statsTimeperiodFormats('decade'));
    }

    /**
     * Test statsGetTimeStamps
     */
    public function testStatsGetTimeStamps(): void
    {
        $date = new \Cake\I18n\FrozenTime(new \DateTimeImmutable('2019-06-20'));
        $results = $this->StatsBehavior->statsGetTimeStamps($date);
        $expected = [
            'year' => '2019',
            'month' => '201906',
            'week' => '201925',
            'day' => '20190620',
            'hour' => '2019062000',
        ];
        $this->assertSame($expected, $results);

        $results = $this->StatsBehavior->statsGetTimeStamps();
        $expected = ['year', 'month', 'week', 'day', 'hour'];
        $this->assertSame($expected, array_keys($results));
    }

    /**
     * Test statsGetTimeperiods
     */
    public function testStatsGetTimeperiods(): void
    {
        $results = $this->StatsBehavior->statsGetTimeperiods();

        $expected = [
            'year',
           'month',
           'week',
           'day',
           'hour',
        ];

        $this->assertSame($expected, $results);
    }

    /**
     * Test statsGetEntityCounts
     */
    public function testStatsGetEntityCounts(): void
    {
        // existing
        $date = new \Cake\I18n\FrozenTime(new \DateTimeImmutable('2019-06-20'));
        $results = $this->StatsBehavior->statsGetEntityCounts('PenTest.Results.open', $date, 1, 'day');

        $this->assertIsArray($results);

        $this->assertInstanceOf(\Sis\Stats\Model\Entity\StatsEntity::class, $results['entity']);
        $this->assertSame(1, $results['entity']->get('id'));
        $this->assertSame('PenTest.Results.open', $results['entity']->get('key'));
        $this->assertSame('Open', $results['entity']->get('name'));

        $this->assertIsArray($results['counts']);

        $this->assertInstanceOf(\Sis\Stats\Model\Entity\StatsCount::class, $results['counts'][20190619]);
        $this->assertSame(1, $results['counts'][20190619]->get('stats_entity_id'));
        $this->assertSame('day', $results['counts'][20190619]->get('time_period'));
        $this->assertSame(20190619, $results['counts'][20190619]->get('time_stamp'));
        $this->assertSame(0, $results['counts'][20190619]->get('time_count'));
    }

    /**
     * Test statsGetEntityCounts
     */
    public function testStatsGetEntityCountsNew(): void
    {
        // existing
        $date = new \Cake\I18n\FrozenTime(new \DateTimeImmutable('2019-06-21'));
        $results = $this->StatsBehavior->statsGetEntityCounts('PenTest.Results.dontexist', $date, 1, 'day');

        $this->assertIsArray($results);

        $this->assertInstanceOf(\Sis\Stats\Model\Entity\StatsEntity::class, $results['entity']);
        $this->assertSame(0, $results['entity']->get('id'));
        $this->assertSame('PenTest.Results.dontexist', $results['entity']->get('key'));
        $this->assertSame('PenTest Results dontexist', $results['entity']->get('name'));

        $this->assertIsArray($results['counts']);
        $this->assertSame(2, count($results['counts']));

        $this->assertInstanceOf(\Sis\Stats\Model\Entity\StatsCount::class, $results['counts'][20190620]);
        $this->assertInstanceOf(\Sis\Stats\Model\Entity\StatsCount::class, $results['counts'][20190621]);
        $this->assertSame(0, $results['counts'][20190620]->get('stats_entity_id'));
        $this->assertSame('day', $results['counts'][20190620]->get('time_period'));
        $this->assertSame(20190620, $results['counts'][20190620]->get('time_stamp'));
        $this->assertSame(0, $results['counts'][20190620]->get('time_count'));
    }

    /**
     * Test statsGetEntityCounts
     */
    public function testStatsGetEntityCountsBadTimeperiod(): void
    {
        // existing
        $date = new \Cake\I18n\FrozenTime(new \DateTimeImmutable('2019-06-21'));
        $results = $this->StatsBehavior->statsGetEntityCounts('PenTest.Results.dontexist', $date, 1, 'decade');

        $this->assertIsArray($results);

        $this->assertInstanceOf(\Sis\Stats\Model\Entity\StatsEntity::class, $results['entity']);
        $this->assertSame(0, $results['entity']->get('id'));
        $this->assertSame('PenTest.Results.dontexist', $results['entity']->get('key'));
        $this->assertSame('PenTest Results dontexist', $results['entity']->get('name'));

        $this->assertIsArray($results['counts']);
        $this->assertTrue(empty($results['counts']));
    }

    /**
     * Test statsGetEntitiesCounts
     */
    public function testStatsGetEntitiesCounts(): void
    {
        $date = new \Cake\I18n\FrozenTime(new \DateTimeImmutable('2019-06-20'));
        $results = $this->StatsBehavior->statsGetEntitiesCounts(['PenTest.Results.open', 'PenTest.Results.pastDue'], $date, 1, 'day');

        $this->assertIsArray($results);

        $this->assertIsArray($results['PenTest.Results.open']);
        $this->assertInstanceOf(\Sis\Stats\Model\Entity\StatsEntity::class, $results['PenTest.Results.open']['entity']);
        $this->assertSame(1, $results['PenTest.Results.open']['entity']->get('id'));
        $this->assertSame('PenTest.Results.open', $results['PenTest.Results.open']['entity']->get('key'));
        $this->assertSame('Open', $results['PenTest.Results.open']['entity']->get('name'));
        $this->assertIsArray($results['PenTest.Results.open']['counts']);
        $this->assertSame(2, count($results['PenTest.Results.open']['counts']));

        $this->assertIsArray($results['PenTest.Results.pastDue']);
        $this->assertInstanceOf(\Sis\Stats\Model\Entity\StatsEntity::class, $results['PenTest.Results.pastDue']['entity']);
        $this->assertSame(2, $results['PenTest.Results.pastDue']['entity']->get('id'));
        $this->assertSame('PenTest.Results.pastDue', $results['PenTest.Results.pastDue']['entity']->get('key'));
        $this->assertSame('Past Due', $results['PenTest.Results.pastDue']['entity']->get('name'));
        $this->assertIsArray($results['PenTest.Results.pastDue']['counts']);
        $this->assertSame(2, count($results['PenTest.Results.pastDue']['counts']));
    }
}
