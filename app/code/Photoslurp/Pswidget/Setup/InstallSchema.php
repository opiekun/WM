<?php
/**
 * Copyright © 2015 Photoslurp. All rights reserved.
 */

namespace Photoslurp\Pswidget\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        $table  = $installer->getConnection()
            ->newTable($installer->getTable('photoslurp_pswidget_items'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'user_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null]
            )
            ->addColumn(
                'widget_enable',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['default' => null]
            )
            ->addColumn(
                'widget_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null]
            )
            ->addColumn(
                'widget_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null]
            )
            ->addColumn(
                'album_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null]
            )
            ->addColumn(
                'page_limit',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['default' => 15]
            )
            ->addColumn(
                'page_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null]
            )
            ->addColumn(
                'show_submit',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['default' => null]
            )
            ->addColumn(
                'social_icons',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['default' => true]
            )
            ->addColumn(
                'image_height',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['default' => 200]
            )
            ->addColumn(
                'image_width',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['default' => 200]
            )
            ->addColumn(
                'toc_link',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null]
            )
            ->addColumn(
                'random_order',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['default' => null]
            )
            ->addColumn(
                'allow_empty',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['default' => null]
            )
            ->addColumn(
                'autoscroll_limit',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['default' => null]
            )
            ->addColumn(
                'add_photos_img',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null]
            )
            ->addColumn(
                'position',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null]
            )




            ->addColumn(
                'autoscroll_limit',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['default' => null]
            )
            ->addColumn(
                'enable_ga',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['default' => null]
            )
            ->addColumn(
                'init_delay',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['default' => null]
            )
            ->addColumn(
                'social_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['default' => null]
            )
            ->addColumn(
                'thumb_overlay',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['default' => null]
            )
            ->addColumn(
                'varying_thumb_sizes',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['default' => null]
            )
            ->addColumn(
                'auto_scroll_carousel',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['default' => null]
            )
            ->addColumn(
                'one_photo_per_line',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['default' => null]
            )
            ->addColumn(
                'analytics_cookie_ttl',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['default' => null]
            )
            ->addColumn(
                'submission_form_css_url',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null]
            )
            ->addColumn(
                'strict_products',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['default' => null]
            )
            ->addColumn(
                'empty_threshold',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['default' => null]
            )
            ->addColumn(
                'in_stock_only',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['default' => null]
            )
            ->addColumn(
                'rights_cleared_only',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['default' => null]
            )
            ->addColumn(
                'assigned_only',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['default' => null]
            )
            ->addColumn(
                'visible_products',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['default' => null]
            )
            ->addColumn(
                'collections',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null]
            )
            ->addColumn(
                'additional_params',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null]
            );

        $installer->getConnection()->createTable($table);


        $tableLang  = $installer->getConnection()
            ->newTable($installer->getTable('photoslurp_pswidget_items_lang'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'id_widget',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['default' => null]
            )
            ->addColumn(
                'id_lang',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['default' => null]
            )
            ->addColumn(
                'submit_text',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null]
            )
            ->addColumn(
                'note_add_pics_text',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null]
            )
            ->addColumn(
                'shop_this_look_text',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null]
            )
            ->addColumn(
                'cta_button',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null]
            )
            ->addColumn(
                'load_more_text',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['default' => null]
            );
        $installer->getConnection()->createTable($tableLang);


        $installer->endSetup();
    }
}
