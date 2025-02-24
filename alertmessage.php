<?php

/**
 * 2007-2024 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>  
 *  @copyright 2007-2024 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class AlertMessage extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'alertmessage';
        $this->tab = 'checkout';
        $this->version = '1.0.0';
        $this->author = 'Ainhoa ';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Alert Message');
        $this->description = $this->l('Here is my perfect module!');

        $this->ps_versions_compliancy = array('min' => '8.0', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayExpressCheckout');
    }

    public function uninstall()
    {

        return parent::uninstall();
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookDisplayExpressCheckout($params)
    {
        $fecha = date('d-m-Y', strtotime('l jS'));
        $cliente = new Customer($this->context->customer->id);
        $carrito = new Cart($this->context->cart->id);
        $festivo = date('d-m-Y', strtotime('25-01-2025'));
        $diferencia = $festivo-$fecha;


        $this->context->smarty->assign([
            "fecha" => $fecha,
            "nombre" => $cliente->firstname,
            "cantidad" => $carrito->nbProducts(),
            "diferencia" => $diferencia,
            "festivo" => $festivo
        ]);

        return $this->display(__FILE__, 'views/templates/hook/alertmessage.tpl');
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . '/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');
    }
}
