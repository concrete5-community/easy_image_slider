/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global _ , Concrete, ConcreteAlert, ConcreteEvent, ConcreteFileManager, CCM_DISPATCHER_FILENAME, CCM_SECURITY_TOKEN */
(function() {
'use strict';

function EasySlideManager(sliderEntriesContainer, options) {
    var my = this;
    my.sliderEntriesContainer = sliderEntriesContainer;
    my.options = options;
    my._templateSlide = _.template($('#SlideTemplate').html());
    my.isFirstFile = true;
    my.filesetAlreadyChoosed = [];
    my.$easyImageSave = $('#easy_image_save');
    my.fileUploadArgs = {
        url: CCM_DISPATCHER_FILENAME + '/ccm/system/file/upload',
        dataType: 'json',
        formData: {'ccm_token': CCM_SECURITY_TOKEN},
        add: function(_e, data) {
            var uploadFile = data.files[0];
            if (!(/\.(gif|jpg|jpeg|tiff|png)$/i).test(uploadFile.name)) {
                alert(my.options.i18n.imageOnly);
                return;
            }
            if (uploadFile.size > 6000000) {
                alert(my.options.i18n.imageSize);
                return;
            }
            data.submit();
        },
        send: function(e, data) {
            // Cette fonction est appelé au moment où les fichiers on été choisis.
            // Si c'est le premier de la liste, on initie le knob sur cette element (celui qui a initié les upload)
            if (my.isFirstFile) {
                my.initUploadActionOnItem($(e.target));
            }
            // Si il y a plus d'un chargement, on a besoin de créer pour chaque un nouvel objet
            // Dans ce cas on le crée et on l'assigne à la variable data.newItem
            // On initie aussi le knob
            else {
                data.newItem = my.fillSlideTemplate();
                my.initUploadActionOnItem(data.newItem);
            }
            my.isFirstFile = false;
        },
        progress: function(e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10),
                target = data.newItem ? data.newItem : $(e.target);
            if (progress < 95) {
                target.find('.knob').val(progress).change();
            } else {
                target.find('.knob').val(100).change();
                if(!target.find('canvas').is('.out')) {
                    target.find('canvas').addClass('out');
                    target.find('.process').addClass('in');
                }
            }
        },
        done: function(e, data) {
            var target = data.newItem ? data.newItem : $(e.target);
            $.get(
                my.options.getFileDetailDetailUrl,
                {
                    fID: (data.result.files || data.result)[0].fID
                },
                function(file) {
                    my.fillSlideTemplate(file, target);
                },
                'json'
            );
        },
        fail: function(r) {
            //$.fn.dialog.closeTop();
            var message = r.responseText;
            try {
                message = $.parseJSON(message).errors.join('<br/>');
            } catch(e) {}
            ConcreteAlert.dialog('Error', message);
        },
        stop: function() {
            my.isFirstFile = true;
            my.fillSlideTemplate();
        }
    };

    $.fn.replaceWithPush = function(a) {var $a = $(a);this.replaceWith($a);return $a;};

    // -- Quand on choisi un Fileset -- \\
    $('#fsID').on('change', function() {
        var t = $(this),
            v = t.val();
        if ($.inArray(v, my.filesetAlreadyChoosed) > -1) {
            if (!confirm(my.options.i18n.filesetAlreadyPicked)) {
                return;
            }
        }
        my.filesetAlreadyChoosed.push(v);
        $.get(
            my.options.getFilesetImagesURL,
            {
                fsID: v
            },
            function(data) {
                if(data.length) {
                    $.each(data, function(_i, f) {
                        my.fillSlideTemplate(f);
                        my.refreshManager();
                    });
                    t.val(0);
                }
            },
            'json'
        );
    });

    $('.option-button').on('click', function() {
        var button = $(this),
            slide = $('#' + button.attr('rel'));
        my.optionsSlideToggle(button, slide);
    });

    $('.easy_image_options_close').on('click',function() {
        $('.options-content').slideUp();
    });

    // -- On crée le premier ou le dernier carré -- //
    my.fillSlideTemplate();
    my.$easyImageSave.on('click', function() {
        $('#ccm-block-form').submit();
        ConcreteEvent.fire('EditModeExitInlineSaved');
        ConcreteEvent.fire('EditModeExitInline', {
            action: 'save_inline'
        });
    });
	$('#easy_image_cancel').on('click', function() {
		ConcreteEvent.fire('EditModeExitInline');
		Concrete.getEditMode().scanBlocks();
	});
}
EasySlideManager.prototype = {
    // Quand on clique sur le cadre on déclenche l'ouverture du navigateur de fichier Navigateur
    attachUploadEvent: function($obj) {
        // On lance le fileupload
        $obj.fileupload(this.fileUploadArgs);
        var $inputfile = $obj.find('input.browse-file');
        $obj.find('.upload-file').on('click',function(e) {
            e.preventDefault();
            $inputfile.click();
        });
    },
    initUploadActionOnItem: function($obj) {
        $obj.find('.knob').knob();
        $obj.find('.add-file-control').hide();
    },
    attachFileManagerLaunch: function($obj) {
        var my = this;
        $obj.find('.add-file').click(function(event) {
            event.preventDefault();
            var Launcher = $obj;
            ConcreteFileManager.launchDialog(function(data) {
                // data : Object {fID: "1"}
                $.get(
                    my.options.getFileDetailDetailUrl,
                    {
                        fID: data.fID
                    },
                    function(file) {
                        if (file.type === "Image") {
                            $.fn.dialog.hideLoader();
                            my.fillSlideTemplate(file, Launcher);
                           // On ajoute un nouvel element vide a coté
                            my.fillSlideTemplate();
                            return;
                        }
                        $.fn.dialog.hideLoader();
                        alert('You must select an image file only');
                    },
                    'json'
                );
            });
        });
    },
    fillSlideTemplate: function(file, $element) {
        var defaults = {
            fID: '',
            title: '',
            link_url: '',
            cID: '',
            description: '',
            sort_order: '',
            image_url: '',
            image_link: '',
            image_link_text: '',
            image_thumbnail_width:'',
            image_bg_color:'#fff'
        };
        if (file) {
            $.extend(
                defaults,
                {
                    fID: file.fID,
                    title: file.title,
                    description: file.description,
                    sort_order: '',
                    image_url: file.urlInline,
                    image_link: file.image_link,
                    image_link_text: file.image_link_text,
                    image_thumbnail_width: file.image_thumbnail_width,
                    image_bg_color:file.image_bg_color
                }
            );
        }
        var newSlide;
        if ($element) {
            //  on est dans le cas ou l'utilisateur a uploadé ou choisi un fichier
            // dans ce cas on replace le carré vide par un element rempli avec image et tout le toutim
           newSlide = $element.replaceWithPush(this._templateSlide(defaults));
        } else {
            // On ajoute un nouveau avec ou sans infos
            this.sliderEntriesContainer.append(this._templateSlide(defaults));
            newSlide = $('.slide-item').last();
        }
        // Si le carré est vide, il faut activer les bouton de remplissage
        if (!file) {
            this.attachFileManagerLaunch(newSlide);
            this.attachUploadEvent(newSlide, this.fileUploadArgs);
        } else {
            this.attachDelete(newSlide);
            this.initImageEdit(newSlide,file);
            // Retirer tous l'input file qui ne vient que surcharger les données envoyées
            // Et qui n'ont servi qu'a uploder un fichier
            newSlide.find('.browse-file').remove();
            // Mettre à jour le fID
            newSlide.find('.image-fID').val(file.fID);
        }
        this.refreshManager();
        return newSlide;
    },
    attachDelete: function($obj) {
        var my = this;
        $obj.find('.remove-item').click(function() {
            if (!confirm(my.options.i18n.confirmDeleteImage)) {
                return;
            }
            $(this).closest('.slide-item').remove();
            my.refreshManager();
        });
    },
    initImageEdit: function($obj, file) {
        var my = this;
        $obj.find(".dialog-launch").dialog();
        $obj.find('.editable-click').editable({
            ajaxOptions: {dataType: 'json'},
            emptytext: my.options.i18n.none,
            showbuttons: true,
            url: my.options.saveFieldURL,
            params:{fID: file.fID},
            pk: '_x',
        });
        // Faire en sorte que les infos restent visibles quand on edite le titre ou la description
        $obj.find('.editable-click').on('shown', function(data) {
            $(data.target).closest('.slide-item-toolbar').addClass('active');
        });
        $obj.find('.editable-click').on('hidden', function(data) {
            $(data.target).closest('.slide-item-toolbar').removeClass('active');
        });
        // L'editeur de couleurs
        var editor = $obj.find('#ccm-colorpicker-bg-' + file.fID);
        editor.spectrum({
            appendTo: $obj,
            className: "ccm-widget-colorpicker",
            showButtons: true,
            showInitial: true,
            showInput: true,
            allowEmpty: true,
            preferredFormat: "rgba",
            showAlpha: false,
            change: function(color) {
                my.saveFileAttribute(file.fID, 'image_bg_color', color.toHexString());
            }
        });
    },
    refreshManager: function() {
        // Deplacer le carré vide à la dernière place
        $('.slide-item').not('.filled').appendTo(this.sliderEntriesContainer);
        // On permet la réorganisation
        this.sliderEntriesContainer.sortable({handle: ".handle"});
        // On regarde si on desactive ou pas le bouton submit
        // en comptant les carré rempli d'image
        if(!$('.slide-item.filled').size()) {
            this.$easyImageSave.addClass('disabled');
        } else if (this.$easyImageSave.is('.disabled')) {
            this.$easyImageSave.removeClass('disabled');
        }
    },
    saveFileAttribute: function(_fID, atHandle, _value) {
        $.post(
            this.options.saveFieldURL,
            {
                fID: _fID,
                name: atHandle,
                value:_value
            }
        );
    },
    optionsSlideToggle: function(button, slide) {
        button = button.toggleClass('active');
        $('.option-button').not(button).removeClass('active');
        $('.options-content').not(slide).slideUp();
        if(button.is('.active')) {
            slide.slideDown();
        } else {
            slide.slideUp();
        }
    }
};

window.EasySlideManager = EasySlideManager;

})();
