/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_CustomRegistration
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
/*jshint jquery:true*/
define([
        "jquery",
        'mage/translate',
        "mage/template",
        "mage/mage",
        "mage/calendar",
    ], function ($, $t,mageTemplate, alert) {
        'use strict';
        $.widget('mage.customDependableField', {

            options: {
                optionTemp : '',
                dependTemp : '',
                allowedExtensionTmp : '#allowed_extension_template',
                dependableExtnTemp: '#dependable_allowed_extension_template',
                tabMainContent : '#custom_attribute_tabs_main_content',
                customFiledOption : '.customfield_options',
                dependableWrapper : '#dependable_advanced_fieldset-wrapper',
                baseFieldSelector : '#customfields_base_fieldset',
                frontEndClass : '.field-frontend_class',
                isRequired : '.field-is_required',
                frontEndInput: '#customfields_frontend_input',
                dependInput : '#dependable_frontend_input',
            },
            _create: function () {
                var self = this;
                this.options.dependTemp = $(self.options.tabMainContent).find(self.options.dependableWrapper).parent('.entry-edit');
                this.options.optionTemp = $(self.options.tabMainContent).find(self.options.customFiledOption);
                $(self.options.tabMainContent).find(self.options.customFiledOption).hide();
                $(self.options.tabMainContent).find(self.options.dependableWrapper).parent('.entry-edit').remove();

                if (self.options.codeSignal == 1) {
                    $(self.options.baseFieldSelector).find("#customfields_attribute_code").attr('disabled','true');
                    $(self.options.baseFieldSelector).find(self.options.frontEndInput).attr('disabled','true');
                    $(self.options.baseFieldSelector).find(self.options.frontEndClass).show();
                    $(self.options.baseFieldSelector).find(self.options.isRequired).show();
                }

                if (self.options.dependableSignal == 1) {
                    $(self.options.tabMainContent).append(this.options.dependTemp);
                    $(self.options.tabMainContent).find(self.options.dependInput).attr('disabled','true');
                    $(self.options.tabMainContent).find("#dependable_attribute_code").attr('disabled','true');
                    $(self.options.baseFieldSelector).find(self.options.frontEndClass).hide();
                    $(self.options.baseFieldSelector).find(".field-is_required").hide();
                }
                if (self.options.fileSignal == "1") {
                    $(self.options.baseFieldSelector).find(self.options.frontEndClass).hide();
                    $(self.options.baseFieldSelector).find(self.options.isRequired).show();
                    var progressTmpl = mageTemplate(self.options.allowedExtensionTmp),
                                  tmpl;
                        tmpl = progressTmpl({
                                data: {
                                    allowedextension: self.options.fileExtensionValue
                                }
                            });
                        $('.field-frontend_input').after(tmpl);
                }
                if (self.options.selectSignal == "1") {
                    $(self.options.tabMainContent).find(self.options.customFiledOption).show();
                    $(self.options.baseFieldSelector).find("#customfields_frontend_class").attr('disabled','true');
                }
                if (self.options.textareaSignal == "1") {
                    $(self.options.baseFieldSelector).find("#customfields_frontend_class").attr('disabled','true');
                }
                if (self.options.booleanSignal == "1") {
                    $(self.options.baseFieldSelector).find("#customfields_frontend_class").attr('disabled','true');
                    $(self.options.baseFieldSelector).find(self.options.isRequired).hide();
                }
                $(self.options.frontEndInput).on('change',function () {
                    self._manageFields($(this));
                });
                /* if dependable templates have some data then add template automatically */
                if (self.options.dependableTextareaSignal == "1") {
                    $(self.options.baseFieldSelector).find("#customfields_dependable_validation_type").attr('disabled','true');
                }
                if (self.options.dependableBoolean == "1") {
                    $(self.options.baseFieldSelector).find("#customfields_dependable_validation_type").attr('disabled','true');
                    $(self.options.tabMainContent).find(".field-is_required").hide();
                }
                if (self.options.dependableSelectoptionSignal == "1") {
                    $(self.options.tabMainContent).find(self.options.dependableWrapper).append(this.options.optionTemp);
                    this.options.optionTemp.show();
                    $("#custom_attribute_tabs_main_content").find("#dependable_frontend_class").attr('disabled','true');
                }
                if (self.options.textSignal != "1") {
                  $("#custom_attribute_tabs_main_content").find("#dependable_frontend_class").attr('disabled','true');
                }
                if (self.options.dependableAllowedAxtensionsSignal == "1") {
                    var progressTmpl = mageTemplate(self.options.dependableExtnTemp),
                                  tmpl;
                        tmpl = progressTmpl({
                                data: {
                                    dependExtension:self.options.dependableExtnValue
                                }
                            });
                        $('#dependable_frontend_input').parent().parent().after(tmpl);
                        $('#dependable_inputtype').after(tmpl);
                }
                $(self.options.tabMainContent).delegate(self.options.dependInput,'change',function () {
                    self._manageDependFields($(this));
                });
            },
            _manageFields: function (thisval) {
                var self = this;
                var dependTemp = $(self.options.tabMainContent).find(self.options.dependableWrapper).parent('.entry-edit');
                $(thisval).parents(self.options.baseFieldSelector).find(".dependable_type_container").remove();
                $(thisval).parents(self.options.baseFieldSelector).find(".selectoption_type_container").remove();
                self.options.optionTemp.remove();
                $(thisval).parents(self.options.tabMainContent).find(".allowed_extensions_type_container").remove();
                dependTemp.remove();
                if ($(thisval).val() == 'text') {
                    $(self.options.tabMainContent).find(self.options.customFiledOption).hide();
                    $(self.options.tabMainContent).find(self.options.dependableWrapper).parent('.entry-edit').remove();
                    $(thisval).parents(self.options.baseFieldSelector).find(self.options.frontEndClass).show();
                    $(thisval).parents(self.options.baseFieldSelector).find(".field-is_required").show();
                    $(thisval).parents(self.options.baseFieldSelector).find("#customfields_frontend_class").removeAttr('disabled');
                }
                if ($(thisval).val() == 'textarea') {
                    dependTemp.remove();
                    self.options.optionTemp.remove();
                    $(thisval).parents(self.options.baseFieldSelector).find(self.options.frontEndClass).show();
                    $(thisval).parents(self.options.baseFieldSelector).find(".field-is_required").show();
                    $(thisval).parents(self.options.baseFieldSelector).find("#customfields_frontend_class").attr('disabled','true');
                }
                if ($(thisval).val() == 'boolean') {
                    dependTemp.remove();
                    self.options.optionTemp.remove();
                    $(thisval).parents(self.options.baseFieldSelector).find(self.options.frontEndClass).show();
                    $(thisval).parents(self.options.baseFieldSelector).find(".field-is_required").hide();
                    $(thisval).parents(self.options.baseFieldSelector).find("#customfields_frontend_class").attr('disabled','true');
                }
                if ($(thisval).val() == 'dependable') {
                    $(self.options.tabMainContent).append(this.options.dependTemp);
                    $(thisval).parents(self.options.baseFieldSelector).find(self.options.frontEndClass).hide();
                    $(thisval).parents(self.options.baseFieldSelector).find(".field-is_required").hide();
                    self.options.optionTemp.remove();
                }
                 if ($(thisval).val() == 'date') {
                    self.options.optionTemp.remove();
                    $(self.options.tabMainContent).find(self.options.dependableWrapper).parent('.entry-edit').remove();
                    $(thisval).parents(self.options.baseFieldSelector).find(self.options.frontEndClass).show();
                    $(thisval).parents(self.options.baseFieldSelector).find(".field-is_required").show();
                    $(thisval).parents(self.options.baseFieldSelector).find("#customfields_frontend_class").attr('disabled','true');
                }
                if ($(thisval).val() == 'select' || $(thisval).val() == 'multiselect') {
                    $(thisval).parents(self.options.baseFieldSelector).find(self.options.frontEndClass).show();
                    $(thisval).parents(self.options.baseFieldSelector).find(".field-is_required").show();
                    $(thisval).parents(self.options.baseFieldSelector).find("#customfields_frontend_class").attr('disabled','true');
                    $(self.options.tabMainContent).find("#customfields_base_fieldset-wrapper").append(self.options.optionTemp);
                    self.options.optionTemp.show();
                    $(self.options.tabMainContent).find(self.options.dependableWrapper).parent('.entry-edit').remove();
                }
                if ($(thisval).val() == 'file' || $(thisval).val() == 'image') {
                    var extn = 'jpg,png,gif';
                    if ($(thisval).val() == 'file') {
                        extn = 'pdf,zip,doc';
                    }
                    $(thisval).parents(self.options.baseFieldSelector).find(self.options.frontEndClass).hide();
                    $(thisval).parents(self.options.baseFieldSelector).find(".field-is_required").show();
                    $(self.options.tabMainContent).find(self.options.customFiledOption).hide();
                    $(self.options.tabMainContent).find(self.options.dependableWrapper).parent('.entry-edit').remove();
                    var progressTmpl = mageTemplate(self.options.allowedExtensionTmp),
                              tmpl;
                    tmpl = progressTmpl({
                            data: {allowedextension: extn}
                        });
                    $('.field-frontend_input').after(tmpl);
                }
            },
            _manageDependFields: function (thisval) {
                var self = this;
                $(thisval).parents(self.options.tabMainContent).find(".dependable_selectoption_type_container").remove();
                $(thisval).parents(self.options.tabMainContent).find(".dependable_allowed_extensions_type_container").remove();
                var dependable_value = $(thisval).val();
                if (dependable_value == "dependable_select" || dependable_value == "dependable_multiselect") {
                    $(thisval).parents(self.options.baseFieldSelector).find(".field-dependable_required_field").show();
                    $(thisval).parents(self.options.tabMainContent).find("#dependable_frontend_class").attr('disabled','true');
                    $(self.options.tabMainContent).find(self.options.dependableWrapper).append(self.options.optionTemp);
                    self.options.optionTemp.show();
                }
                if (dependable_value == "dependable_file" || dependable_value == "dependable_image") {
                    $(thisval).parents(self.options.tabMainContent).find("#dependable_frontend_class").attr('disabled','true');
                    // $(thisval).parents(self.options.tabMainContent).find(".field-is_required").hide();
                    $(thisval).parents(self.options.dependableWrapper).find(".field-is_required").show();
                    var extn = 'jpg,png,gif';
                    if ($(thisval).val() == 'dependable_file') {
                        extn = 'pdf,zip,doc';
                    }
                    var progressTmpl = mageTemplate(self.options.dependableExtnTemp),
                              tmpl;
                    tmpl = progressTmpl({
                            data: {dependExtension:extn}
                        });
                    $('#dependable_frontend_input').parents('.field-frontend_input').after(tmpl);
                    self.options.optionTemp.remove();
                }
                if ($(thisval).val() == 'dependable_text') {
                   $(thisval).parents(self.options.tabMainContent).find(".field-is_required").show();
                    $(thisval).parents(self.options.tabMainContent).find("#dependable_frontend_class").removeAttr('disabled');
                    self.options.optionTemp.remove();
                }
                 if ($(thisval).val() == 'dependable_textarea') {
                    $(thisval).parents(self.options.tabMainContent).find(".field-is_required").show();
                    $(thisval).parents(self.options.tabMainContent).find("#dependable_frontend_class").attr('disabled','true');
                    self.options.optionTemp.remove();
                }
                if ($(thisval).val() == 'dependable_date') {
                    $(thisval).parents(self.options.tabMainContent).find(".field-is_required").show();
                    $(thisval).parents(self.options.tabMainContent).find("#dependable_frontend_class").attr('disabled','true');
                    self.options.optionTemp.remove();
                }
                if ($(thisval).val() == 'dependable_boolean') {
                    $(thisval).parents(self.options.tabMainContent).find(".field-is_required").hide();
                    $(thisval).parents(self.options.tabMainContent).find("#dependable_frontend_class").attr('disabled','true');
                    self.options.optionTemp.remove();
                }
            },
        });
    return $.mage.customDependableField;
    });
