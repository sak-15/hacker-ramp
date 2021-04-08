<?php

namespace SwiLook;

use Doctrine\Common\Collections\ArrayCollection;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\SearchBundle\Condition\CategoryCondition;
use Shopware\Components\Theme\LessDefinition;

class SwiLook extends Plugin
{

    /**
     * @param InstallContext $context
     */
    public function install(InstallContext $context)
    {
        $installer = $this->container->get('shopware.emotion_component_installer');

        $shopTheLookElement = $installer->createOrUpdate(
            $this->getName(),
            'Shop The Look',
            [
                'name' => 'Shop The Look',
                'template' => 'component_shop_the_look',
                'xtype' => 'emotion-components-shop-the-look',
                'cls' => 'emotion-shop-the-look-element',
                'description' => 'Adds a Shop the Look Slider to the Shopping World'
            ]
        );

        $shopTheLookElement->createNumberField(
            [
                'name' => 'category_id',
                'fieldLabel' => 'Category ID',
                'supportText' => 'Enter the ID of the Category from the given Articles.',
                'allowBlank' => false
            ]
        );

        $shopTheLookElement->createMediaField(
            [
                'name' => 'preview_image',
                'fieldLabel' => 'Preview Image',
                'valueField' => 'virtualPath',
                'supportText' => 'The Image which is shown in the Shopping World an can be clicked.',
                'allowBlank' => false
            ]
        );

    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Controllers_Widgets_Emotion_AddElement' => 'onEmotionAddElement',
            'Theme_Compiler_Collect_Plugin_Less' => 'onCollectLessFiles',
            'Theme_Compiler_Collect_Plugin_Javascript' => 'addJsFiles'
        ];
    }

    /**
     * @param \Enlight_Event_EventArgs $args
     */
    public function onEmotionAddElement(\Enlight_Event_EventArgs $args)
    {
        /*
         * Get the Input Data of the Element to manipulate them.
         */
        $element = $args->get('element');

        if ($element['component']['xType'] !== 'emotion-components-shop-the-look') {
            return;
        }

        $data = $args->getReturn();

        /*
         * Get all Articles by the given Category with the Shopware Search Bundle
         */
        $context = $this->container->get('shopware_storefront.context_service')->getProductContext();
        $search = $this->container->get('shopware_search.product_search');

        $criteria  = new Criteria();
        $criteria->addCondition(new CategoryCondition([$data['category_id']]));

        $result = $search->search(
            $criteria,
            $context
        );

        /*
         * Loop through each given Article from the Category we searched
         * and save it as an Array. Since the SearchBundle returns an Object,
         * we have to transform it to an Array.
         */
        $products = [];
        foreach($result->getProducts() as $product) {
            $products[] = json_decode(json_encode($product), true);
        }

        /*
         * Put the created array from the Loop,  and save it to the $data Variable,
         * which we called in the first Step.
         */
        $data['products'] = $products;

        //dump($data);

        /*
         * Return all Data back to the View.
         */
        $args->setReturn($data);

    }

    /**
     * @return ArrayCollection
     */
    public function onCollectLessFiles()
    {
        $lessDir = __DIR__ . '/Resources/Views/frontend/_public/src/less/';

        $less = new LessDefinition(
            array(),
            array(
                $lessDir . 'shopthelook.less'
            )
        );

        return new ArrayCollection(array($less));
    }

    /**
     * Provide the file collection for js files
     *
     * @return ArrayCollection
     */
    public function addJsFiles()
    {
        $jsFiles = [
            __DIR__ . '/Resources/Views/frontend/_public/src/js/shop_the_look.js'
        ];
        return new ArrayCollection($jsFiles);
    }


}
