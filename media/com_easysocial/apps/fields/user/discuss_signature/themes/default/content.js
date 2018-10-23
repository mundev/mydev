<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>

ed.require(['edq', 'markitup'], function($){
    EasyDiscuss.bbcode = [
            
            {
                name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_BOLD');?>",
                key: 'B',
                openWith: '[b]',
                closeWith: '[/b]',
                className: 'markitup-bold'
            },
            
            {
                name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_ITALIC');?>",
                key: 'I',
                openWith: '[i]',
                closeWith: '[/i]',
                className: 'markitup-italic'
            },
            
            {
                name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_UNDERLINE');?>",
                key: 'U',
                openWith: '[u]',
                closeWith: '[/u]',
                className: 'markitup-underline'
            },
            
            {separator: '---------------' },
            

            
         
            {separator: '---------------'},
            
            {
                name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_BULLETED_LIST');?>",
                openWith: '[list]\n[*]',
                closeWith: '\n[/list]',
                className: 'markitup-bullet'
            },
            
            {
                name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_NUMERIC_LIST');?>",
                openWith: '[list=[![Starting number]!]]\n[*]',
                closeWith: '\n[/list]',
                className: 'markitup-numeric'
            },
           
            {
                name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_LIST_ITEM');?>",
                openWith: '[*] ',
                className: 'markitup-list'
            },
            {separator: '---------------' },
           
            {
                name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_QUOTES');?>",
                openWith: '[quote]',
                closeWith: '[/quote]',
                className: 'markitup-quote'
            },
           
            {
                name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_CODE');?>",
                openWith: '[code type="markup"]\n',
                closeWith: '\n[/code]',
                className: 'markitup-code'
            },
           
            {
                name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_GIST');?>",
                openWith: '[gist type="php"]\n',
                closeWith: '\n[/gist]',
                className: 'markitup-gist'
            },
            
            {separator: '---------------' },
            

            {
                name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_HAPPY');?>",
                openWith: ':D ',
                className: 'markitup-happy'
            },
            {
                name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_SMILE');?>",
                openWith: ':) ',
                className: 'markitup-smile'
            },
            {
                name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_SURPRISED');?>",
                openWith: ':o ',
                className: 'markitup-surprised'
            },
            {
                name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_TONGUE');?>",
                openWith: ':p ',
                className: 'markitup-tongue'
            },
            {
                name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_UNHAPPY');?>",
                openWith: ':( ',
                className: 'markitup-unhappy'
            },
            {
                name: "<?php echo JText::_('COM_EASYDISCUSS_BBCODE_WINK');?>",
                openWith: ';) ',
                className: 'markitup-wink'
            }
        ]

    $(document).ready(function(){

        var textarea = $("[data-ed-composer] > textarea");
        $(textarea).markItUp({
            markupSet: EasyDiscuss.bbcode
        });
    });

});
