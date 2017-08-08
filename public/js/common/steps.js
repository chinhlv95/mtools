$(function  ()  {
            var currentStep = 1;
            
            $('#wizardTab li a').click(function()   {
                return false;
            });
            
            $('#nextStep').click(function() {
    
                currentStep++;
                
                if(currentStep == 2)    {
                    $('#wizardTab li:eq(1) a').tab('show');
                    $('#wizardProgress').css("width","100%");
                    
                    $('#prevStep').attr('disabled',false);
                    $('#prevStep').removeClass('disabled');
                    
                    $('#nextStep').attr('disabled',true);
                    $('#nextStep').addClass('disabled');
                }
               
                
                return false;
            });
            
            $('#prevStep').click(function() {
        
                currentStep--;
                
                if(currentStep == 1)    {
                
                    $('#wizardTab li:eq(0) a').tab('show');
                    $('#wizardProgress').css("width","66%");
                        
                    $('#prevStep').attr('disabled', true);
                    $('#prevStep').addClass('disabled');
                    $('#nextStep').attr('disabled',false);
                    $('#nextStep').removeClass('disabled');
                    
                    $('#wizardProgress').css("width","50%");
                }
                
                
                return false;
            });
        });