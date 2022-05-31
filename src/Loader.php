<?php

namespace WPGraphQL\Extensions\TaxQuery;

class Loader
{
    public static function init()
    {
        define('WP_GRAPHQL_TAX_QUERY', 'initialized');
        (new Loader())->bind_hooks();
    }

    function bind_hooks()
    {
        add_action(
            'graphql_register_types',
            [$this, 'tq_action_register_types'],
            9,
            0
        );

        add_filter(
            'graphql_map_input_fields_to_wp_query',
            [$this, 'tq_filter_map_offset_to_wp_query_args'],
            10,
            2
        );
    }

    function tq_filter_map_offset_to_wp_query_args(
        array $query_args,
        array $where_args
    ) {
        $args = $where_args['taxQuery'];

        if (! empty($args)) {
            $tax_query = $query_args['tax_query'] ?? [];

            if (count($args['taxArray']) > 1) {
                $tax_query['relation'] = $args['relation'];
            }

            if (! empty($args['taxArray']) && is_array($args['taxArray'])) {
                foreach ($args['taxArray'] as $value) {
                    if (empty($value['terms'])) {
                        // Skip if no terms set
                        continue;
                    }

                    $tax_query[] = [
                        'taxonomy' => $value['taxonomy'],
                        'field' => $value['field'],
                        'terms' => array_map(function ($term) use ($value) {
                            // If 'field' is 'term_id' or 'term_taxonomy_id' convert to integer
                            if ('term_id' === $value['field'] || 'term_taxonomy_id' === $value['field']) {
                                return intval($term);
                            }

                            return $term;
                        }, $value['terms']),
                        'include_children' => $value['includeChildren'] ?? false,
                        'operator' => $value['operator'] ?? 'IN',
                    ];
                }

                if (! empty($tax_query)) {
                    $query_args['tax_query'] = $tax_query;
                }
            }
        }

        unset($query_args['taxQuery']);

        return $query_args;
    }

    static function add_post_type_fields(\WP_Post_Type $post_type_object)
    {
        $type = ucfirst($post_type_object->graphql_single_name);
        register_graphql_fields("RootQueryTo${type}ConnectionWhereArgs", [
            'taxQuery' => [
                'type' => 'TaxQuery',
            ],
        ]);
    }

    function tq_action_register_types()
    {
        foreach (\WPGraphQL::get_allowed_post_types() as $post_type) {
            self::add_post_type_fields(get_post_type_object($post_type));
        }

        register_graphql_enum_type('TaxQueryField', [
            'description' => __(
                'Which field to select term by',
                'wp-graphql-tax-query'
            ),
            'values' => [
                'ID' => [
                    'name' => 'ID',
                    'value' => 'term_id',
                ],
                'NAME' => [
                    'name' => 'NAME',
                    'value' => 'name',
                ],
                'SLUG' => [
                    'name' => 'SLUG',
                    'value' => 'slug',
                ],
                'TAXONOMY_ID' => [
                    'name' => 'TAXONOMY_ID',
                    'value' => 'term_taxonomy_id',
                ],
            ],
        ]);

        register_graphql_enum_type('TaxQueryOperator', [
            'values' => [
                'IN' => [
                    'name' => 'IN',
                    'value' => 'IN',
                ],
                'NOT_IN' => [
                    'name' => 'NOT_IN',
                    'value' => 'NOT IN',
                ],
                'AND' => [
                    'name' => 'AND',
                    'value' => 'AND',
                ],
                'EXISTS' => [
                    'name' => 'EXISTS',
                    'value' => 'EXISTS',
                ],
                'NOT_EXISTS' => [
                    'name' => 'NOT_EXISTS',
                    'value' => 'NOT EXISTS',
                ],
            ],
        ]);

        register_graphql_input_type('TaxArray', [
            'fields' => [
                'taxonomy' => [
                    'type' => 'TaxonomyEnum',
                ],
                'field' => [
                    'type' => 'TaxQueryField',
                ],
                'terms' => [
                    'type' => ['list_of' => 'String'],
                    'description' => __('A list of term slugs', 'wp-graphql-tax-query'),
                ],
                'includeChildren' => [
                    'type' => 'Boolean',
                    'description' => __('Whether or not to include children for hierarchical taxonomies. Defaults to false to improve performance (note that this is opposite of the default for WP_Query).',
                        'wp-graphql-tax-query'),
                ],
                'operator' => [
                    'type' => 'TaxQueryOperator',
                ],
            ],
        ]);

        register_graphql_input_type('TaxQuery', [
            'description' => __(
                'Offset pagination input type',
                'wp-graphql-tax-query'
            ),
            'fields' => [
                'relation' => [
                    'type' => 'RelationEnum',
                ],
                'taxArray' => [
                    'type' => ['list_of' => 'TaxArray'],
                ],
            ],
        ]);
    }
}
