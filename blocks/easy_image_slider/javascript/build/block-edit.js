   debug = false;

   var easy_slide_manager = function  (sliderEntriesContainer) {

        // sliderEntriesContainer = $('.slide-items');
        // Le template
        var _templateSlide = _.template($('#SlideTemplate').html());
        var is_first_file = true;
        filesetAlreadyChoosed = new Array();
        // Les options du FileUpload
        var args = {
            url: CCM_DISPATCHER_FILENAME + '/ccm/system/file/upload',
            dataType: 'json',
            formData: {'ccm_token': CCM_SECURITY_TOKEN},
            add: function (e, data) {
                var goUpload = true;
                var uploadFile = data.files[0];
                if (!(/\.(gif|jpg|jpeg|tiff|png)$/i).test(uploadFile.name)) {
                    alert (ccmi18n.imageOnly);
                    goUpload = false;
                }
                if (uploadFile.size > 6000000) {
                    alert (ccmi18n.imageSize);
                    goUpload = false;
                }
                if (goUpload == true) {
                    data.submit();
                }
            },
            send : function (e, data) {
                // Cette fonction est appelé au moment où les fichiers on été choisis.
                // Si c'est le premier de la liste, on initie le knob sur cette element (celui qui a initié les upload)
                if (is_first_file) {
                    initUploadActionOnItem($(e.target));
                }
                // Si il y a plus d'un chargement, on a besoin de créer pour chaque un nouvel objet
                // Dans ce cas on le crée et on l'assigne à la variable data.newItem
                // On initie aussi le knob
                else {
                    newItem = fillSlideTemplate();
                    data.newItem = newItem;
                    initUploadActionOnItem(newItem);
                }
                is_first_file = false;
          
            },
            progress: function(e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                
                var target = data.newItem ? data.newItem : $(e.target);
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
            done: function (e, data) {
                var target = data.newItem ? data.newItem : $(e.target);
                $.get(getFileDetailDetailJson,{fID:data.result[0].fID}, function(file) {
                    fillSlideTemplate(file,target);
                },'json');                 
            },
            fail: function(r,data) {
                //jQuery.fn.dialog.closeTop();
                var message = r.responseText;
                try {
                    message = jQuery.parseJSON(message).errors.join('<br/>');
                } catch (e) {}
                ConcreteAlert.dialog('Error', message);
            },
            stop: function (e) {
                is_first_file = true;
                fillSlideTemplate();
            }
        };

        $.fn.replaceWithPush = function(a) {var $a = $(a);this.replaceWith($a);return $a;};


        // Quand on clique sur le cadre on déclenche l'ouverture du navigateur de fichier Navigateur
        var attachUploadEvent = function ($obj) {
            // On lance le fileupload
            $obj.fileupload(args);    
            $inputfile = $obj.find('input.browse-file');
            $obj.find('.upload-file').on('click',function(e){
                e.preventDefault();
                $inputfile.click();
            });
        }

        var initUploadActionOnItem = function ($obj) {
            $obj.find('.knob').knob();
            $obj.find('.add-file-control').hide();
        }


        var attachDelete = function($obj) {
            $obj.find('.remove-item').click(function(){
                var deleteIt = confirm(ccmi18n.confirmDeleteImage);
                if(deleteIt == true) {
                   $(this).closest('.slide-item').remove();
                   refreshManager ();
                }
            });
        }

        var attachFileManagerLaunch = function($obj) {

            $obj.find('.add-file').click(function(event){
                event.preventDefault();
                var Launcher = $obj;
                ConcreteFileManager.launchDialog(function (data) {
                    // data : Object {fID: "1"}
                    $.get(getFileDetailDetailJson,{fID:data.fID}, function(file) {
                        if(file.type == "Image"){
                            jQuery.fn.dialog.hideLoader();
                            fillSlideTemplate(file,Launcher);
                           // On ajoute un nouvel element vide a coté
                            fillSlideTemplate();
                            return;
                        } ;
                        jQuery.fn.dialog.hideLoader();
                        alert('You must select an image file only')

                    },'json');                    
                });
            });
        }

        var initImageEdit = function ($obj,file) {
            $obj.find(".dialog-launch").dialog();
            $obj.find('.editable-click').editable({
                ajaxOptions: {dataType: 'json'},
                emptytext: ccmi18n.none,
                showbuttons: true,
                url: saveFieldURL,
                params:{fID:file.fID},
                pk: '_x',

            });
            // Faire en sorte que les infos restent visibles quand on edite le titre ou la description
            $obj.find('.editable-click').on('shown', function (data) {
                    $(data.target).closest('.slide-item-toolbar').addClass('active');
            });
            $obj.find('.editable-click').on('hidden', function (data) {
                    $(data.target).closest('.slide-item-toolbar').removeClass('active');
            });
            // L'editeur de couleurs
            var editor = $obj.find('#ccm-colorpicker-bg-' + file.fID);
            editor.spectrum({appendTo : $obj,className:"ccm-widget-colorpicker",showButtons: true, showInitial:true,showInput:true,allowEmpty:true,preferredFormat:"rgba",showAlpha:false, change: function(color) { saveFileAttribute(file.fID,'image_bg_color',color.toHexString()); } });
            // "cancelText":"<?php echo t('Cancel') ?>","chooseText":"<?php echo t('Choose') ?>""clearText":"<?php echo t('Clear Color Selection') ?>",

        }

        fillSlideTemplate = function (file,$element) {

            var defaults = { fID: '', title: '', link_url: '', cID: '', description: '', sort_order: '', image_url: '', image_link: '', image_link_text: '', image_thumbnail_width:'', image_bg_color:'#fff'};
            if (file) $.extend(defaults, {fID: file.fID, title: file.title, description: file.description, sort_order: '', image_url: file.urlInline, image_link: file.image_link, image_link_text: file.image_link_text, image_thumbnail_width: file.image_thumbnail_width, image_bg_color:file.image_bg_color});

            if ($element) {
                //  on est dans le cas ou l'utilisateur a uploadé ou choisi un fichier
                // dans ce cas on replace le carré vide par un element rempli avec image et tout le toutim
               var newSlide = $element.replaceWithPush(_templateSlide(defaults));

            } else {

                // On ajoute un nouveau avec ou sans infos
                sliderEntriesContainer.append(_templateSlide(defaults));
                var newSlide = $('.slide-item').last();

            }
            // Si le carré est vide, il faut activer les bouton de remplissage
            if (!file) {
                attachFileManagerLaunch(newSlide);
                attachUploadEvent(newSlide);
            } else {
                attachDelete(newSlide);
                initImageEdit(newSlide,file);
                // Retirer tous l'input file qui ne vient que surcharger les données envoyées
                // Et qui n'ont servi qu'a uploder un fichier
                newSlide.find('.browse-file').remove();
                // Mettre à jour le fID
                newSlide.find('.image-fID').val(file.fID);
            }
            
            refreshManager ();

            return newSlide;
        }

        var refreshManager = function () {
            // Deplacer le carré vide à la dernière place
            $('.slide-item').not('.filled').appendTo(sliderEntriesContainer);
            // On permet la réorganisation
            sliderEntriesContainer.sortable({handle: ".handle"});
            // On regarde si on desactive ou pas le bouton submit
            // en comptant les carré rempli d'image
            var b = $('#easy_image_save'); 
            if(!$('.slide-item.filled').size()) {
                b.addClass('disabled');
            } else if (b.is('.disabled')) {
                b.removeClass('disabled');
            };

        }
        var saveFileAttribute = function (_fID, atHandle, _value) {
            $.post(saveFieldURL,{fID:_fID,name:atHandle,value:_value });
        }        

        // -- Quand on choisi un Fileset -- \\

        $('#fsID').change(function(){
            var t = $(this);
            var v = t.val();
            if ($.inArray(v, filesetAlreadyChoosed) > -1) {
                var addImages = confirm(ccmi18n.filesetAlreadyPicked );
                if(addImages == false) {
                    return;
                }
            }
            filesetAlreadyChoosed.push(v);
            $.get(getFilesetImagesURL,{fsID:v}, function(data) {
                if(data.length) {
                    $.each(data,function(i,f){
                        fillSlideTemplate(f);
                        refreshManager ();        

                    });
                    t.val(0);
                }
            },'json');
            
        });


        $('.option-button').on('click',function(e){
            var button = $(this);
            var slide = $('#' + button.attr('rel'));
            optionsSlideToggle(button,slide);
        });

        function optionsSlideToggle (button,slide) {
            button = button.toggleClass('active');

             $('.option-button').not(button).removeClass('active') ;
            $('.options-content').not(slide).slideUp();
            
            var newButton = $(button.attr('id'));

            if(button.is('.active')) {
                slide.slideDown();
            } else {
                slide.slideUp();
            }
        }

        $('.easy_image_options_close').on('click',function(e){
            $('.options-content').slideUp();
        });

        // -- On crée le premier ou le dernier carré -- //
        fillSlideTemplate();
};

function l() {
    if(debug==true) {
        for (var i=0; i < arguments.length; i++) {
            console.log(arguments[i]);
        }
    } 
}


var submitBlockForm = function () {
    $('#ccm-block-form').submit();
    ConcreteEvent.fire('EditModeExitInlineSaved');
    ConcreteEvent.fire('EditModeExitInline', {
        action: 'save_inline'
    });    
} 

function cancelBlockForm () {
    ConcreteEvent.fire('EditModeExitInline');
    Concrete.getEditMode().scanBlocks();
}
