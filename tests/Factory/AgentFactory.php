<?php

namespace App\Tests\Factory;

use App\Entity\Agent;
use App\Repository\AgentRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Agent>
 *
 * @method static Agent|Proxy createOne(array $attributes = [])
 * @method static Agent[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Agent[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Agent|Proxy find(object|array|mixed $criteria)
 * @method static Agent|Proxy findOrCreate(array $attributes)
 * @method static Agent|Proxy first(string $sortedField = 'id')
 * @method static Agent|Proxy last(string $sortedField = 'id')
 * @method static Agent|Proxy random(array $attributes = [])
 * @method static Agent|Proxy randomOrCreate(array $attributes = [])
 * @method static Agent[]|Proxy[] all()
 * @method static Agent[]|Proxy[] findBy(array $attributes)
 * @method static Agent[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Agent[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static AgentRepository|RepositoryProxy repository()
 * @method Agent|Proxy create(array|callable $attributes = [])
 */
final class AgentFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'email' => self::faker()->email(),
            'roles' => [],
            'password' => self::faker()->password()
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Agent $agent): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Agent::class;
    }
}
