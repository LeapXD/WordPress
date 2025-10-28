<?php
/**
 * Block context
 *
 * @package Kicks
 *
 * @var array $attributes
 * @var WP_Block $block
 * @var \XD\Types\XD_Template_Props $block_props
 * @var string $content
 * @var array $context
 * @var array<WP_Block> $inner_blocks
 * @var array $parent
 * @var array $templates
 * @var string $variation
 * @var \XD\Types\XD_Block_Wrap $wrap
 * @var array $xd_context
 * @phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
 */

use XD\Types\XD_Link;

$link         = new XD_Link();
$link->rel    = $attributes['rel'];
$link->target = $attributes['target'];
$link->url    = $attributes['url'];
$link->icon   = $attributes['icon'];

$context['link'] = $link;
