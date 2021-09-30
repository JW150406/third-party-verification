@if(Auth::user()->access_level=='tpvagent' && $twilio_id != '' )
    <script src="{{ asset('js/taskrouter.min.js') }}"></script>
    <script src="{{ asset('js/twilio.min.js') }}"></script>
    <script src="{{ asset('js/main.js') }}?v=3"></script>
    <script src="{{ asset('js/agent.js') }}?v=9"></script>


    <div id="user-status-box" style="">
        <header class="clearfix">
            <div class="status-select">
                <div class="new-status-box">
                    <select class="vodiapicker form-control" id="status-to-change">

                    </select>
                    <div class="status-outer">
                        <button class="btn-select" value=""></button>
                        <div class="status-block">
                            <ul id="status-select"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="chat" style="display:none;">
            <div class="user-chat-history">
                <div class="chat-message clearfix">
                    <div class="chat-message-content clearfix">
                        <div class="row hidden" id="connected-agent-row">
                            <div class="call_from_data">
                            </div>
                            <div class="text-center">
                                <div class="call-outer-height">
                                    <div class="call_duration"> <time>00:00:00</time></div>
                                </div>
                                <button id="hangup-call-button" class="btn btn-lg btn-danger hidden" disabled>
                                    Hangup
                                </button>
                                <div class="hidden audio-controls">
                                    <span>
                                        <a href="javascript:void(0);" class="mute-unmute mute-call hidden ">
                                            <svg aria-hidden="true" data-prefix="fas" data-icon="microphone-alt-slash" class="svg-inline--fa fa-microphone-alt-slash fa-w-20" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                                                <path fill="currentColor" d="M633.82 458.1L476.26 336.33C488.74 312.21 496 284.98 496 256v-48c0-8.84-7.16-16-16-16h-16c-8.84 0-16 7.16-16 16v48c0 17.92-3.96 34.8-10.72 50.2l-26.55-20.52c3.1-9.4 5.28-19.22 5.28-29.67h-43.67l-41.4-32H416v-32h-85.33c-5.89 0-10.67-3.58-10.67-8v-16c0-4.42 4.78-8 10.67-8H416v-32h-85.33c-5.89 0-10.67-3.58-10.67-8v-16c0-4.42 4.78-8 10.67-8H416c0-53.02-42.98-96-96-96s-96 42.98-96 96v45.36L45.47 3.37C38.49-2.05 28.43-.8 23.01 6.18L3.37 31.45C-2.05 38.42-.8 48.47 6.18 53.9l588.36 454.73c6.98 5.43 17.03 4.17 22.46-2.81l19.64-25.27c5.41-6.97 4.16-17.02-2.82-22.45zM400 464h-56v-33.78c11.71-1.62 23.1-4.28 33.96-8.08l-50.4-38.96c-6.71.4-13.41.87-20.35.2-55.85-5.45-98.74-48.63-111.18-101.85L144 241.31v6.85c0 89.64 63.97 169.55 152 181.69V464h-56c-8.84 0-16 7.16-16 16v16c0 8.84 7.16 16 16 16h160c8.84 0 16-7.16 16-16v-16c0-8.84-7.16-16-16-16z"></path>
                                            </svg>
                                        </a>
                                        <a href="javascript:void(0);" class="mute-unmute unmute-call ">
                                            <svg aria-hidden="true" data-prefix="fas" data-icon="microphone-alt" class="svg-inline--fa fa-microphone-alt fa-w-11" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512">
                                                <path fill="currentColor" d="M336 192h-16c-8.84 0-16 7.16-16 16v48c0 74.8-64.49 134.82-140.79 127.38C96.71 376.89 48 317.11 48 250.3V208c0-8.84-7.16-16-16-16H16c-8.84 0-16 7.16-16 16v40.16c0 89.64 63.97 169.55 152 181.69V464H96c-8.84 0-16 7.16-16 16v16c0 8.84 7.16 16 16 16h160c8.84 0 16-7.16 16-16v-16c0-8.84-7.16-16-16-16h-56v-33.77C285.71 418.47 352 344.9 352 256v-48c0-8.84-7.16-16-16-16zM176 352c53.02 0 96-42.98 96-96h-85.33c-5.89 0-10.67-3.58-10.67-8v-16c0-4.42 4.78-8 10.67-8H272v-32h-85.33c-5.89 0-10.67-3.58-10.67-8v-16c0-4.42 4.78-8 10.67-8H272v-32h-85.33c-5.89 0-10.67-3.58-10.67-8v-16c0-4.42 4.78-8 10.67-8H272c0-53.02-42.98-96-96-96S80 42.98 80 96v160c0 53.02 42.98 96 96 96z"></path>
                                            </svg>
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row hidden" id="complete-task-row">
                            <div class="text-center not-ready-btn">
                                <button id="complete-task-button" class="btn btn-success">
                                    Ready
                                </button>
                                <button id="not-ready-button" class="btn btn-secondary" style="font-size: 14px !important;">
                                    Not Ready
                                </button>
                                <div class="status-select-not-ready" style="display: none">
                                    <div class="new-status-box">
                                        <select class="vodiapicker-not-ready form-control" id="status-to-change-not-ready" style="display: none">

                                        </select>
                                        <div class="status-outer">
                                            <div class="status-block-not-ready">
                                                <ul id="status-select-not-ready"></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>

                        </div>
                    </div> <!-- end chat-message-content -->
                </div> <!-- end chat-message -->

            </div> <!-- end user-chat-history -->

        </div> <!-- end chat -->

    </div> <!-- end user-status-box -->







    <script>
        window.tokenurl = "{{ route('conference-token')}}";
        window.getclientagents = "{{ route('tpvagent.clientagents')}}";
        window.getformscript = "{{ route('tpvagent.clientformscript')}}";
        window.leadquestions = "{{ route('tpvagent.leadquestions')}}";
        window.createlead = "{{ route('tpvagent.createlead')}}";



        (function() {
            // $('#user-status-box header').on('click', function() {
            //     $('#user-status-box  .chat').slideToggle(300, 'swing');
            //     $('#user-status-box header .chat-message-counter').fadeToggle(300, 'swing');
            // });

            // $('.chat-close').on('click', function(e) {
            //     e.preventDefault();
            //     $('#user-status-box').fadeOut(300);
            // });

        })();
    </script>

    <script>
        //test for getting url value from attr
        // var img1 = $('.test').attr("data-thumbnail");
        // console.log(img1);

        //test for iterating over child elements
        var langArray = [];
        $('.vodiapicker option').each(function() {
            var img = $(this).attr("data-thumbnail");
            var text = this.innerText;
            var value = $(this).val();
            var item = '<li><img src="' + img + '" alt="" value="' + value + '"/><span>' + text + '</span></li>';
            langArray.push(item);
        })

        $('#status-select').html(langArray);

        //Set the button value to the first el of the array
        $('.btn-select').html(langArray[0]);
        $('.btn-select').attr('value', 'en');

        //change button stuff on click
        $('#status-select li').click(function() {
            var img = $(this).find('img').attr("src");
            var value = $(this).find('img').attr('value');
            var text = this.innerText;
            var item = '<li><img src="' + img + '" alt="" /><span>' + text + '</span></li>';
            $('.btn-select').html(item);
            $('.btn-select').attr('value', value);
            $(".status-block").slideUp();
            //console.log(value);
        });

        $(".btn-select").click(function() {
            $(".status-block").slideToggle();
        });

        //check local storage for the lang
        var sessionLang = localStorage.getItem('lang');
        if (sessionLang) {
            //find an item with value of sessionLang
            var langIndex = langArray.indexOf(sessionLang);
            $('.btn-select').html(langArray[langIndex]);
            $('.btn-select').attr('value', sessionLang);
        } else {
            var langIndex = langArray.indexOf('ch');
            console.log(langIndex);
            $('.btn-select').html(langArray[langIndex]);
            //$('.btn-select').attr('value', 'en');
        }
    </script>



    <!-- <script>
      // status-select dropdown
      jQuery().ready(function() {
        /* Custom select design */
        jQuery('.drop-down').append('<div class="button"></div>');
        jQuery('.drop-down').append('<ul class="select-list"></ul>');
        jQuery('.drop-down select option').each(function() {
          var bg = jQuery(this).css('background-image');
          jQuery('.select-list').append('<li class="clsAnchor" value="' + jQuery(this).val() + '" class="' + jQuery(this).attr('class') + '" style=background-image:' + bg + '>' + jQuery(this).text() + '</li>');
        });
        jQuery('.drop-down .button').html('<li style=background-image:' + jQuery('.drop-down select').find(':selected').css('background-image') + '>' + jQuery('.drop-down select').find(':selected').text() + '</li>' + '<a href="javascript:void(0);" class="select-list-link"><img src="https://img.icons8.com/metro/26/000000/chevron-down.png"></a>');
        jQuery('.drop-down ul li').each(function() {
          if (jQuery(this).find('li').text() == jQuery('.drop-down select').find(':selected').text()) {
            jQuery(this).addClass('active');
          }
        });
        jQuery('.drop-down .select-list li').on('click', function() {
          var dd_text = jQuery(this).text();
          var dd_img = jQuery(this).css('background-image');
          var dd_val = jQuery(this).attr('value');
          jQuery('.drop-down .button').html('<li style=background-image:' + dd_img + '>' + dd_text + '</li>' + '<a href="javascript:void(0);" class="select-list-link"><img src="https://img.icons8.com/metro/26/000000/chevron-down.png"></a>');
          jQuery('.drop-down .select-list li').parent().removeClass('active');
          jQuery(this).parent().addClass('active');
          $('.drop-down select[name=options]').val(dd_val);
          $('.drop-down .select-list').slideUp();
        });
        jQuery('.drop-down .button').on('click', function() {
          jQuery('.drop-down ul').slideToggle();
        });
        /* End */
      });
    </script> -->


@endif