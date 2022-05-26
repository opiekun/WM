<?php
namespace Photoslurp\Pswidget\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $columns = [
                'style_submissionform_colourtop',
                'style_submissionform_colourbutton',
                'style_submissionform_font',
                'style_taggingtitle_font_family',
                'style_taggingtitle_font_style',
                'style_taggingtitle_font_weight',
                'style_taggingtitle_font_color',
                'style_taggingtitle_font_size',
                'style_thumbnail_bg_color',
                'style_thumbnail_border_color',
                'style_carousel_bg_color',
                'style_popup_bg_color',
                'style_popup_title_font_family',
                'style_popup_title_font_style',
                'style_popup_title_font_weight',
                'style_popup_title_font_color',
                'style_source_font_family',
                'style_source_font_style',
                'style_source_font_weight',
                'style_source_font_color',
                'style_productcaptionshop_font_family',
                'style_productcaptionshop_font_style',
                'style_productcaptionshop_font_weight',
                'style_productcaptionshop_font_color',
                'style_productdescription_font_family',
                'style_productdescription_font_style',
                'style_productdescription_font_weight',
                'style_productdescription_font_color'
            ];

            foreach ($columns as $column) {
                $setup->getConnection()->addColumn(
                    $setup->getTable('photoslurp_pswidget_items'),
                    $column,
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'default' => null,
                        'length' => 255,
                        'comment' => 'style'
                    ]
                );
            }

            $setup->getConnection()->addColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_custom',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'default' => null,
                    'comment' => 'style'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_custom_enable',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                    'default' => null,
                    'comment' => 'style'
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.0.2', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'bundled_jquery',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                    'default' => null,
                    'comment' => 'jQuery widget'
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.0.3', '<')) {
            $setup->getConnection()->changeColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'collections',
                'collection',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'enable_ga',
                'enable_g_a',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN
                ]
            );
            $setup->getConnection()->changeColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'analytics_cookie_ttl',
                'analytics_cookie_TTL',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'submission_form_css_url',
                'submission_form_CSS_URL',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_custom',
                'css',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'cookie_domain',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('photoslurp_pswidget_items_lang'),
                'add_photos_img',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'photos_align',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'utm_params',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['default' => null]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'utm_source',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'utm_medium',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'utm_campaign',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'utm_content',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('photoslurp_pswidget_items_lang'),
                'posted_by_text',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('photoslurp_pswidget_items_lang'),
                'view_and_shop_text',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'same_tab_links',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['default' => null]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'cross_domain_tracking',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['default' => null]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_productcaptionshop_font_family',
                'style_popup_productCaptionShop_font_family',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_productcaptionshop_font_style',
                'style_popup_productCaptionShop_font_style',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_productcaptionshop_font_weight',
                'style_popup_productCaptionShop_font_weight',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_productcaptionshop_font_color',
                'style_popup_productCaptionShop_font_color',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_productdescription_font_family',
                'style_popup_productDescription_font_family',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_productdescription_font_style',
                'style_popup_productDescription_font_style',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_productdescription_font_weight',
                'style_popup_productDescription_font_weight',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_productdescription_font_color',
                'style_popup_productDescription_font_color',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_taggingtitle_font_family',
                'style_taggingTitle_font_family',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_taggingtitle_font_style',
                'style_taggingTitle_font_style',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_taggingtitle_font_weight',
                'style_taggingTitle_font_weight',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_taggingtitle_font_color',
                'style_taggingTitle_font_color',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_taggingtitle_font_size',
                'style_taggingTitle_font_size',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_submissionform_colourtop',
                'style_submissionForm_colourTop',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_submissionform_colourbutton',
                'style_submissionForm_colourButton',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_submissionform_font',
                'style_submissionForm_font',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_source_font_family',
                'style_popup_source_font_family',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_source_font_style',
                'style_popup_source_font_style',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_source_font_weight',
                'style_popup_source_font_weight',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_source_font_color',
                'style_popup_source_font_color',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.0.7', '<')) {
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items_lang'), 'note_add_pics_text');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'note_add_pics_icons');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'image_height');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'image_width');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'bundled_jquery');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'social_count');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'one_photo_per_line');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'fix_widget_analytics');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'submission_form_url');
            $setup->getConnection()->addColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'theme',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'default' => null,
                    'comment' => 'theme'
                ]
            );

            $setup->getConnection()->dropColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_popup_productCaptionShop_font_family'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_popup_productCaptionShop_font_style'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_popup_productCaptionShop_font_weight'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_popup_productCaptionShop_font_color'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_popup_productDescription_font_family'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_popup_productDescription_font_style'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_popup_productDescription_font_weight'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_popup_productDescription_font_color'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_taggingTitle_font_family'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_taggingTitle_font_style'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_taggingTitle_font_weight'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_taggingTitle_font_color'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_taggingTitle_font_size'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_submissionForm_colourTop'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_submissionForm_colourButton'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_submissionForm_font'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_popup_source_font_family'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_popup_source_font_style'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_popup_source_font_weight'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'style_popup_source_font_color'
            );

            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'style_thumbnail_bg_color');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'style_thumbnail_border_color');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'style_carousel_bg_color');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'style_popup_bg_color');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'style_popup_title_font_family');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'style_popup_title_font_style');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'style_popup_title_font_weight');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'style_popup_title_font_color');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'photos_align');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'cross_domain_tracking');
            $setup->getConnection()->addColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'website',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'default' => null,
                    'comment' => 'website'
                ]
            );
        }

        if (version_compare($context->getVersion(), '3.1.0', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'product_type',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                    'default' => null,
                    'comment' => 'enable product type'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'lookbook_product_types',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'default' => null,
                    'comment' => 'lookbook product types'
                ]
            );

            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'allow_empty');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'init_delay');

            $setup->getConnection()->addColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'category',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'default' => null,
                    'comment' => 'category'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'display_in_categories',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'default' => null,
                    'comment' => 'display in categories'
                ]
            );

            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'page_limit');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'album_id');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'show_submit');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'add_photos_img');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'social_icons');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'random_order');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'autoscroll_limit');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'enable_g_a');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'submission_form_CSS_URL');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'thumb_overlay');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'varying_thumb_sizes');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'auto_scroll_carousel');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'analytics_cookie_TTL');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'lightbox');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'toc_link');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'strict_products');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'empty_threshold');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'in_stock_only');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'rights_cleared_only');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'assigned_only');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'visible_products');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'collection');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'cookie_domain');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'utm_params');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'utm_source');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'utm_medium');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'utm_campaign');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'utm_content');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'same_tab_links');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'theme');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'additional_params');
            $setup->getConnection()->dropColumn($setup->getTable('photoslurp_pswidget_items'), 'widget_type');
            $setup->getConnection()->dropTable($setup->getTable('photoslurp_pswidget_items_lang'));
        }
        if (version_compare($context->getVersion(), '3.1.1', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('photoslurp_pswidget_items'),
                'position_category',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null]
            );
        }
        $setup->endSetup();
    }
}
