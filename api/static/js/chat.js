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

function usergroup_id_to_class_name(group_id) {
	if (parseInt(group_id) == 99) {
		return 'chatBot';
	}
	return 'usergroup-' + group_id;
}

function get_csrf() {
	const meta = document.getElementsByTagName('meta');
	for (let i = 0; i < meta.length; i++) {
		if (meta[i].getAttribute('name') === 'csrf-token') {
		return meta[i].getAttribute('content');
		}
	}
	return '';
}

(function() {
	window.addEventListener('DOMContentLoaded', () => {

		var showSpinner = () => {
			var spinner = document.getElementById('shoutspin'),
				c = document.getElementById('shout').children[0],
				emojisel = document.getElementById('emojiselector'),
				shoutvol = document.getElementById('shoutvol'),
				shouttext = document.getElementById('shouttext');
				
			if (c.childElementCount > 1) {
				while (c.children[1])
					c.removeChild(c.children[1]);
			}

			shouttext.disabled = true;
			emojisel.classList.toggle('hidden');
			shoutvol.classList.toggle('hidden');
			
			if (shouttext.value !== '')
				shouttext.value = '';
		
			spinner.classList.toggle('hidden');
		},
		hideSpinner = () => {
			var spinner = document.getElementById('shoutspin'),
				emojisel = document.getElementById('emojiselector'),
				shoutvol = document.getElementById('shoutvol'),
				shouttext = document.getElementById('shouttext');
			
			emojisel.classList.toggle('hidden');
			shoutvol.classList.toggle('hidden');
			spinner.classList.toggle('hidden');

			if (shouttext.value !== '')
				shouttext.value = '';
			
			shouttext.disabled = false;

		},	shout = document.getElementById('shout'),
			chats = shout.children[0],
			form = shout.children[1],
			chat_input = document.getElementById('shouttext'),
			last_shout_id = -1;

		showSpinner();

		var	gs_user_id = "";
		var	gs_username = "";
		var	gs_usergroup = "";
		var first_message = true,
			mouseover_timeout = 0;


			fetch("shoutbox.php", { method: "POST", credentials: 'same-origin',headers: {"Content-Type": "application/json"},body: JSON.stringify({ action: 5,})
		}).then((response) => {
			if (!response.ok) {
			  throw new Error(`HTTP error, status = ${response.status}`);
			}
			return response.json();
		  }).then((data) => {
			gs_user_id = data.gs_user_id;
		gs_username = data.gs_username;
		gs_usergroup = data.gs_usergroup;
		console.log(data);
		console.log(gs_user_id);
		}) .catch((error) => {
			console.log(error.message);
		  });
		
			




		var notification_sound = new Audio('/static/notification.mp3');
		var last_tag_timestamp = new Map();

		var showWarning = (_t1 = null) => {
			if (_t1 !== null)
				clearInterval(_t1);
			
			var conn_closed_label = document.createElement('label'), emojisel = document.getElementById('emojiselector'), shoutvol = document.getElementById('shoutvol');
			conn_closed_label.appendChild(document.createElement('span'));
			conn_closed_label.children[0].innerHTML = 'Your connection is <strong>closed</strong>, please <strong>refresh</strong>';
			shout.children[1].innerHTML = '';
			shout.children[1].appendChild(conn_closed_label);

            while (chats.firstChild)
                chats.removeChild(chats.firstChild);
			
			emojisel.classList.toggle('hidden');
			shoutvol.classList.toggle('hidden');

			if (typeof shoutvol !== 'undefined' && shoutvol !== null && !shoutvol.classList.contains('hidden'))
				shoutvol.classList.add('hidden');
			
			if (typeof emojisel !== 'undefined' && emojisel !== null && !emojisel.classList.contains('hidden'))
				emojisel.classList.add('hidden');
			
            chat_input.disabled = true;

        }, appendMessage = (epoch, user_id, username, group_id, raw_content, autoscroll = true) => {
			var must_scroll = autoscroll && chats.scrollHeight - chats.clientHeight <= chats.scrollTop + 1;
			var local_date = new Date(0);
			local_date.setUTCSeconds(epoch);

			// Initialize the important variables
			var count = chats.childElementCount,
				p = document.createElement('p'),
				a = document.createElement('a'),	
				name = document.createTextNode(username),
				d = document.createElement('span');
				
			// Format the username
			a.setAttribute('href', 'profile.php?id=' + user_id);
			a.setAttribute('target', '_blank');
			a.className = usergroup_id_to_class_name(group_id);
			a.appendChild(name);

			// Format date
			d.textContent = local_date.toLocaleTimeString() + ' ';
			d.className = 'dateTime';

			// Format the message
			p.textContent = ': ' + decodeHtml(raw_content);

			// Create links
			p.innerHTML = anchorme({
				input: p.textContent,
				options: {
					attributes: {
						target: '_blank',
						referrerpolicy: 'no-referrer'
					},
					truncate: 60,
					middleTruncation: true,
					exclude: string => string.startsWith('file://'),
					protocol: 'https://'
				}
			});

			p.insertBefore(a, p.firstChild);
			p.insertBefore(d, p.firstChild);
			if (raw_content.toString().toLowerCase().includes('@' + gs_username.toLowerCase()) && (!last_tag_timestamp.has(username) || ((Date.now() - last_tag_timestamp.get(username)) >= 30000))) {
				last_tag_timestamp.set(username, Date.now());
				p.className = 'rowHighlight';
				try {
					if (!first_message && window.localStorage.getItem('muted') == null) {
						notification_sound.play().catch(function(e) {});
					}
				} catch (err) {}
			} else if ((count % 2) == 0) {
				p.className = 'rowEven';
			} else {
				p.className = 'rowOdd';
			}

			chats.appendChild(p);

			if (chats.childElementCount > 36)
				chats.removeChild(chats.children[1]);

			if (must_scroll) {
				chats.scrollTop = chats.scrollHeight;
			}
		}, fetchMessages = () => {	
			axios({
				method					:	'POST',
				url						:	'/forums/shoutbox.php',
				responseType			:	'json',
				headers: {
					'Accept'			:	'application/json; charset=utf-8',
					'Content-Type'		:	'application/x-www-form-urlencoded',
					'X-Requested-With'	:	'XMLHttpRequest',
					'X-CSRF'			:	get_csrf()
				},
				data: `action=1&id=${last_shout_id !== -1 ? parseInt(last_shout_id) : 0}`
			})
			.then(response => {

				var data = (typeof response.data !== 'undefined') ? response.data : '',
					status = (data !== '' && data.hasOwnProperty('code')) ? parseInt(data.code) : 409;


					for (info of data.messages) 
					{
						
							if (first_message || last_shout_id < parseInt(data['last_id']))
							{
								var msgs = $('#chatmessages');
								if (msgs.toString().includes(info.text))
								    return;
						
								appendMessage(
									info.time,
									info.uid,
									info.username,
									info.usergroup,
									info.text,
									true
								);
                                last_shout_id = parseInt(info.id);
							}	
							
					}

					if (first_message)
						hideSpinner();

					first_message = false;
				
			})
			.catch(error => {
				console.log(error);
				//showWarning(t1);
				//return false;
			});
		}, sendMessage = async(text) => {
			var tosend = text;
			if (typeof text !== 'string')
			{
               console.log("idk some nigga shit");
				//showWarning(t1);
				//return false;
			} else {
				var unixtime = Math.round((new Date()).getTime() / 1000);
				await axios({
					method					:	'POST',
					url						:	'/forums/shoutbox.php',
					responseType			:	'json',
					headers: {
						'Accept'			:	'application/json; charset=utf-8',
						'Content-Type'		:	'application/x-www-form-urlencoded',
						'X-Requested-With'	:	'XMLHttpRequest',
						'X-CSRF'			:	get_csrf()
					},
					data:  JSON.stringify({ action: 2, text: tosend, unix: unixtime})
				})
				.then(response => {

					var data = (typeof response.data !== 'undefined') ? response.data : '',
						status_code = (data.hasOwnProperty('code')) ? parseInt(response.data.code) : null;

					if (status_code !== null && status_code === 200)
					{
						if (data.hasOwnProperty('last_id') && typeof data['last_id'] === 'number') {
                            last_shout_id = data['last_id'];
						} else if (typeof data['last_id'] !== 'undefined') {
							last_shout_id++;
						}
						return true;
					}
					else
					{
						console.log("some nigga shit2")
						//showWarning(t1);
						//return;
					}
				})
				.catch(error => {
					if (error.response.status === 429) {
						var unix = Math.round((new Date()).getTime() / 1000);
						if (error.response.data.hasOwnProperty('message'))
							appendMessage(unix, 19, 'ChatBot', 99, `@${gs_username} We have received too many messages from you in a quite short amount of time. Your last message has NOT been sent. Please, write fewer messages to avoid bothering our other users.`, true);
						return false;
					}
					console.log(error.message);
					//showWarning(t1);
					return false;
				});
			}
		};

		var t1 = setInterval(() => {
			try {
				
				fetchMessages();
			} catch (err) {
				console.log(err.message);
				//showWarning(t1);
				return false;
			}
		}, 2275);

		form.onsubmit = () => {
			return false;
		};
		var fixMessage = function() {
			var max_length = 140;
			if(chat_input.value.length > max_length || chat_input.value.indexOf('\r') >= 0 || chat_input.value.indexOf('\n') >= 0) {
				chat_input.value = chat_input.value.replace(/\n|\r/g, '').substring(0, max_length);
			}
		},
		checkEnter = function(e) {
			fixMessage();
			if((e.keyCode ? e.keyCode : e.which) === 13) {
				if(chat_input.value.length > 0) {
					if (chat_input.value.replace(/\s/g, '') === '')
						return false;
					
					if (sendMessage(chat_input.value)) {
						chat_input.disabled = true;
						if (chat_input.value != '/roll') {
							var seconds = Math.round((new Date()).getTime() / 1000);
							appendMessage(seconds, gs_user_id, gs_username, gs_usergroup, encodeHtml(chat_input.value), true);
						}
					}
                    chat_input.disabled = false;
					chat_input.value = '';
					chat_input.focus();
				}
				return false;
			}
			return true;
		};

		chat_input.onkeydown = checkEnter;
		chat_input.onchange = fixMessage;
		chat_input.style.visibility = 'visible';
		chat_input.disabled = false;

		return false;
	}, false);
})();

$(function(){
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
				fetch('/static/js/emoji.json?v=1')
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
});