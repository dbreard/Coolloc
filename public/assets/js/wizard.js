searchVisible = 0;
transparent = true;

$(document).ready(function () {
    /*  Activate the tooltips      */
    $('[rel="tooltip"]').tooltip();

    $('.wizard-card').bootstrapWizard({
        'tabClass': 'nav nav-pills',
        'nextSelector': '.btn-next',
        'previousSelector': '.btn-previous',
        onInit: function (tab, navigation, index) {

            //check number of tabs and fill the entire row
            var $total = navigation.find('li').length;
            $width = 100 / $total;

            $display_width = $(document).width();

            if ($display_width < 600 && $total > 3) {
                $width = 50;
            }

            navigation.find('li').css('width', $width + '%');

        },
        onNext: function (tab, navigation, index) {
            if (index == 1) {
                return validateFirstStep();
            } else if (index == 2) {
                return validateSecondStep();
            } else if (index == 3) {
                return validateThirdStep();
            } //etc. 

        },
        onTabClick: function (tab, navigation, index) {
            // Disable the posibility to click on tabs
            return false;
        },
        onTabShow: function (tab, navigation, index) {
            var $total = navigation.find('li').length;
            var $current = index + 1;

            var wizard = navigation.closest('.wizard-card');

            // If it's the last tab then hide the last button and show the finish instead
            if ($current >= $total) {
                $(wizard).find('.btn-next').hide();
                $(wizard).find('.btn-finish').show();
            } else {
                $(wizard).find('.btn-next').show();
                $(wizard).find('.btn-finish').hide();
            }
        }
    });

    
    // Prepare the preview for profile picture
    $("#wizard-picture").change(function () {
        readURL(this);
    });


    // Prepare the preview for profile picture
    $("#wizard-picture1").change(function () {
        readURL1(this);
    });

    // Prepare the preview for profile picture
    $("#wizard-picture2").change(function () {
        readURL2(this);
    });

     // Prepare the preview for profile picture
     $("#wizard-picture3").change(function () {
        readURL3(this);
    });

     // Prepare the preview for profile picture
     $("#wizard-picture4").change(function () {
        readURL4(this);
    });

     // Prepare the preview for profile picture
     $("#wizard-picture5").change(function () {
        readURL5(this);
    });

     // Prepare the preview for profile picture
     $("#wizard-picture6").change(function () {
        readURL6(this);
    });

     // Prepare the preview for profile picture
     $("#wizard-picture7").change(function () {
        readURL7(this);
    });

    // Prepare the preview for profile picture
    $("#wizard-picture8").change(function () {
        readURL8(this);
    });

    // Prepare the preview for profile picture
    $("#wizard-picture9").change(function () {
        readURL9(this);
    });
    // Prepare the preview for profile picture
    $("#wizard-picture10").change(function () {
        readURL10(this);
    });
    // Prepare the preview for profile picture
    $("#wizard-picture11").change(function () {
        readURL11(this);
    });
    // Prepare the preview for profile picture
    $("#wizard-picture12").change(function () {
        readURL12(this);
    });
    // Prepare the preview for profile picture
    $("#wizard-picture13a").change(function () {
        readURL13a(this);
    });
    
    
    

    //Function to show image before upload

    function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();


        reader.onload = function (e) {
            $('#wizardPicturePreview').attr('src', e.target.result).fadeIn('slow');
        }
        reader.readAsDataURL(input.files[0]);
    }

    
    }

    function readURL1(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
    
    
            reader.onload = function (e) {
                $('#wizardPicturePreview1').attr('src', e.target.result).fadeIn('slow');
            }
            reader.readAsDataURL(input.files[0]);
        }
    
        
        }
    
    function readURL1(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
    
    
            reader.onload = function (e) {
                $('#wizardPicturePreview1').attr('src', e.target.result).fadeIn('slow');
            }
            reader.readAsDataURL(input.files[0]);
        }
    
        
        }
    
    function readURL2(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
    
    
            reader.onload = function (e) {
                $('#wizardPicturePreview2').attr('src', e.target.result).fadeIn('slow');
            }
            reader.readAsDataURL(input.files[0]);
        }
        
            
    }

    function readURL3(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
    
    
            reader.onload = function (e) {
                $('#wizardPicturePreview3').attr('src', e.target.result).fadeIn('slow');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
            
                
    function readURL4(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
    
    
            reader.onload = function (e) {
                $('#wizardPicturePreview4').attr('src', e.target.result).fadeIn('slow');
            }
            reader.readAsDataURL(input.files[0]);
        }
    
        
    }

    function readURL5(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
    
    
            reader.onload = function (e) {
                $('#wizardPicturePreview5').attr('src', e.target.result).fadeIn('slow');
            }
            reader.readAsDataURL(input.files[0]);
        }
    
        
    }
                        
    function readURL6(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
    
    
            reader.onload = function (e) {
                $('#wizardPicturePreview6').attr('src', e.target.result).fadeIn('slow');
            }
            reader.readAsDataURL(input.files[0]);
        }
    
        
    }
                            
    function readURL7(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
    
    
            reader.onload = function (e) {
                $('#wizardPicturePreview7').attr('src', e.target.result).fadeIn('slow');
            }
            reader.readAsDataURL(input.files[0]);
        }
    
        
    }

    function readURL8(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
    
    
            reader.onload = function (e) {
                $('#wizardPicturePreview8').attr('src', e.target.result).fadeIn('slow');
            }
            reader.readAsDataURL(input.files[0]);
        }
    
        
    }

    function readURL9(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
    
    
            reader.onload = function (e) {
                $('#wizardPicturePreview9').attr('src', e.target.result).fadeIn('slow');
            }
            reader.readAsDataURL(input.files[0]);
        }
    
        
    }

    function readURL10(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
    
    
            reader.onload = function (e) {
                $('#wizardPicturePreview10').attr('src', e.target.result).fadeIn('slow');
            }
            reader.readAsDataURL(input.files[0]);
        }
    
        
    }

    function readURL11(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
    
    
            reader.onload = function (e) {
                $('#wizardPicturePreview11').attr('src', e.target.result).fadeIn('slow');
            }
            reader.readAsDataURL(input.files[0]);
        }
    
        
    }

    function readURL12(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
    
    
            reader.onload = function (e) {
                $('#wizardPicturePreview12').attr('src', e.target.result).fadeIn('slow');
            }
            reader.readAsDataURL(input.files[0]);
        }
    
        
    }

    function readURL13a(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
    
    
            reader.onload = function (e) {
                $('#wizardPicturePreview13a').attr('src', e.target.result).fadeIn('slow');
            }
            reader.readAsDataURL(input.files[0]);
        }
    
        
    }



    //Function to show image before upload

    



    $('[data-toggle="wizard-radio"]').click(function () {
        wizard = $(this).closest('.wizard-card');
        wizard.find('[data-toggle="wizard-radio"]').removeClass('active');
        $(this).addClass('active');
        $(wizard).find('[type="radio"]').removeAttr('checked');
        $(this).find('[type="radio"]').attr('checked', 'true');
    });

    $('[data-toggle="wizard-checkbox"]').click(function () {
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
            $(this).find('[type="checkbox"]').removeAttr('checked');
        } else {
            $(this).addClass('active');
            $(this).find('[type="checkbox"]').attr('checked', 'true');
        }
    });

    $height = $(document).height();
    $('.set-full-height').css('height', $height);


});

function validateFirstStep() {

    $(".wizard-card form").validate({
        rules: {
            firstname: "required",
            lastname: "required",
            email: {
                required: true,
                email: true
            }

            /*  other possible input validations
             ,username: {
             required: true,
             minlength: 2
             },
             password: {
             required: true,
             minlength: 5
             },
             confirm_password: {
             required: true,
             minlength: 5,
             equalTo: "#password"
             },
             
             topic: {
             required: "#newsletter:checked",
             minlength: 2
             },
             agree: "required"
             */

        },
        messages: {
            firstname: "Please enter your First Name",
            lastname: "Please enter your Last Name",
            email: "Please enter a valid email address",
            /*   other posible validation messages
             username: {
             required: "Please enter a username",
             minlength: "Your username must consist of at least 2 characters"
             },
             password: {
             required: "Please provide a password",
             minlength: "Your password must be at least 5 characters long"
             },
             confirm_password: {
             required: "Please provide a password",
             minlength: "Your password must be at least 5 characters long",
             equalTo: "Please enter the same password as above"
             },
             email: "Please enter a valid email address",
             agree: "Please accept our policy",
             topic: "Please select at least 2 topics"
             */

        }
    });

    if (!$(".wizard-card form").valid()) {
        //form is invalid
        return false;
    }

    return true;
}

function validateSecondStep() {

    //code here for second step
    $(".wizard-card form").validate({
        rules: {
        },
        messages: {
        }
    });

    if (!$(".wizard-card form").valid()) {
        console.log('invalid');
        return false;
    }
    return true;

}

function validateThirdStep() {
    //code here for third step


}















