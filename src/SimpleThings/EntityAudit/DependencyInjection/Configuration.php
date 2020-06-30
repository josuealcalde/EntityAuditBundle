<?php

namespace SimpleThings\EntityAudit\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $builder->root('simple_things_entity_audit')
            ->children()
                ->arrayNode('audited_entities')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('global_ignore_columns')
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('table_prefix')->defaultValue('')->end()
                ->scalarNode('table_suffix')->defaultValue('_audit')->end()
                ->scalarNode('revision_field_name')->defaultValue('rev')->end()
                ->scalarNode('revision_type_field_name')->defaultValue('revtype')->end()
                ->scalarNode('revision_table_name')->defaultValue('revisions')->end()
                ->scalarNode('revision_id_field_type')->defaultValue('integer')->end()
                ->arrayNode('service')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('username_callable')->defaultValue('simplethings_entityaudit.username_callable.token_storage')->end()
                        ->scalarNode('impersonate_callable')->defaultValue('simplethings_entityaudit.impersonate_callable.token_storage')->end()
                        ->scalarNode('accesstoken_callable')->defaultValue('simplethings_entityaudit.accesstoken_callable.token_storage')->end()
                        ->scalarNode('ip_callable')->defaultValue('simplethings_entityaudit.ip_callable.request')->end()
                        ->scalarNode('action_callable')->defaultValue('simplethings_entityaudit.action_callable.request')->end()
                ->end()
            ->end()
        ;

        return $builder;
    }
}
