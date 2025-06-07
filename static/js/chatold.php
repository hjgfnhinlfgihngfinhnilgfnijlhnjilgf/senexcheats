/*
    Copyright (GameSense) 2021, all rights reserved.
    The materials contained in this website are protected by applicable copyright and trademark law.
*/
function decodeHtml(html) {
	var txt = document.createElement("textarea");
	txt.innerHTML = html;
	return txt.value;
}
function encodeHtml(str) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}
var oldresponse;
function handleKeyPress(event) {
        if (event.keyCode == 13 && !event.shiftKey) {
            sendMessage();
            try {
                event.preventDefault();
            } catch (e) {
                event.returnValue = false; // IE
            }
            return false;
        }
        return true;
    }

function sendMessage() {
    var message = encodeHtml($("#shouttext").val();
    if (message.length == 0) {
        return;
    }

    if (message) {
        let request = new XMLHttpRequest();
        request.open("POST", 'chat.php', true);
        request.send(JSON.stringify({
			text: encodeHtml($('#shouttext').val()),
			action: 1,
			csrf: $('meta[name="csrf-token"]').attr('content')
		}));
        $.ajax({
            type: 'POST',
            url: 'chat.php',
            async: true,
            success: function(respText) {
                 $('#chatmessages').html(respText);
                 var shout = document.getElementById('shout');
                 var chats = shout.children[0];
                 document.getElementById('shouttext').value = "";
                 setTimeout(() => {  chats.scrollTop = chats.scrollHeight; }, 500);
                 
            }
        });

    }
}

$(function() {
    	try {
		if (window.localStorage.getItem('muted') != null) {
			var _icon = $('#shoutvol').children('i');
			_icon.removeClass('fa-volume-up');
			_icon.addClass('fa-volume-off');
		}
	} catch (e) {}
	$('#shoutvol').click(function() {
		var muted = 0;
		var _icon = $(this).children('i');
		if (_icon.hasClass('fa-volume-up')) {
			_icon.removeClass('fa-volume-up');
			_icon.addClass('fa-volume-off');
			muted = 1;
		} else {
			_icon.removeClass('fa-volume-off');
			_icon.addClass('fa-volume-up');
		}
		try {
			if (muted) {
				window.localStorage.setItem('muted', '1');
			} else {
				window.localStorage.removeItem('muted');
			}
		} catch (e) {

		}
	});

    if ($('#emojiselector').length) {
		var AddEmoji = function (code) {
			var option = document.createElement('option');
			option.innerHTML = code;
			document.getElementById('emojiselector').appendChild(option);
		}
		if (window.localStorage) {
			var cache = localStorage.getItem('emoji_cache');
			if (cache != null) {
				var e = JSON.parse(cache);
				for (var index = 0; index < e.length; index++) {
					AddEmoji(e[index]);
				}
			} else {
				fetch('/static/js/emoji.json?v=3')
				.then(response => response.json())
				.catch(err => console.error(`[gamesense] Error:\t${err.json()}`))
				.then(e => {
					localStorage.setItem('emoji_cache', JSON.stringify(e));

					for (var index = 0; index < e.length; index++) {
						AddEmoji(e[index]);
					}
				});
			}
			
			$('#emojiselector').on('change', () => {
				$('#shouttext').val($('#shouttext').val() + $('#emojiselector').val());
				$('#shouttext').focus();
			});
		}
    }

    var shout = document.getElementById('shout');
    var chats = shout.children[0];
function showWarning(){
    var conn_closed_label = document.createElement('label'), emojisel = document.getElementById('emojiselector'), shoutvol = document.getElementById('shoutvol');
			conn_closed_label.appendChild(document.createElement('span'));
			conn_closed_label.children[0].innerHTML = 'Your connection is <strong>closed</strong>, please <strong>refresh</strong>';
			shout.children[1].innerHTML = '';
			shout.children[1].appendChild(conn_closed_label);

            while (chats.firstChild)
                chats.removeChild(chats.firstChild);
			
			
			shoutvol.classList.toggle('hidden');

			if (typeof shoutvol !== 'undefined' && shoutvol !== null && !shoutvol.classList.contains('hidden'))
				shoutvol.classList.add('hidden');
			

			
}
function Init() {
        $.ajax({
            type: 'POST',
            url: 'chat.php',
            success: function(respText) {
           if (respText.includes('<form id="challenge-form" action="https://gamesense.ltd/check" method="get">')) {
    showWarning();
    return;
}else{
                 $('#chatmessages').html(respText);
                 if (oldresponse != respText){
                 chats.scrollTop = chats.scrollHeight;
                 oldresponse = respText;
                 }
                 }
            }
        })

}

function getData() {
        $.ajax({
            type: 'POST',
            url: 'chat.php',
            success: function(respText) {
           if (respText.includes('<form id="challenge-form" action="https://gamesense.ltd/check" method="get">')) {
    showWarning();
    return;
}else{
                 $('#chatmessages').html(respText);
                 }
            }
        })

}



    Init();
 
    setInterval(function() {
    
    getData();
    }, 1000);
    

});
