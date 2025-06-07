$(function(){
    var options = {
        useEasing: true,
        useGrouping: true,
        separator: '',
        decimal: '.',
        prefix: '',
        suffix: '&nbsp;&euro;',
    };

    function decodeUnicode(str) {
        return decodeURIComponent(atob(str).split('').map(function (c) {
            return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
        }).join(''));
    }

    const strrand = (length = 8) => {
        return Math.random().toString(16).substr(2, length);
    }
    
    var _csrf_token = $('meta[name="csrf-token"]').attr('content');
    
    var ul = document.querySelector('div#brdwelcome.inbox > ul.conl');
    
    var li1 = document.createElement('li');
    var li2 = document.createElement('li');
    var li3 = document.createElement('li');
    
    var span1 = document.createElement('span');
    var span2 = document.createElement('span');
    var span3 = document.createElement('span');
    
    let alertbox = document.getElementById('alert');
    let alertbox2 = document.getElementById('alert2');

    ul.appendChild(li1);
    ul.appendChild(li2);
    ul.appendChild(li3);
    
    li1.appendChild(span1);
    li2.appendChild(span2);
    li3.appendChild(span3);

    function hide(elem) {
		elem.classList.add('hidden');
	}

    function unhide(elem) {
		elem.classList.remove('hidden');
	}

    function mFetch() {
        $.ajax({
            cache: false,
            type: 'POST',
            url: '/forums/admin.php',
            dataType: 'json',
            data: JSON.stringify({
                action: 0 }),
            success: function (data) {
                    if ('success' in data && data.success) {
                        $(span1).html('<a style="">Website logins 24H: ' + data.login.web + '</a>');
                        $(span2).html('<a style="">Client logins 24H: ' + data.login.client + '</a>');
                        $(span3).html('<a style="">Client errors 24H: ' + '0' + '</a>');
                    } else
                        return false;
            },
            fail: function(){
                console.error('[skeetsu] Unable to fetch admin stats')
                if (job !== undefined && job !== null) {
                    job.clearInterval();
                }
                return false;
            }
        });
    }


        // Fetch admin_x stuff
    function mFetchAlerts() {
        unhide(alertbox2);
        hide(alertbox);
        $.ajax({
            cache: false,
            type: 'POST',
            url: '/forums/admin.php',
            data: JSON.stringify({action: 1 }),
        success: function (data) {
                        $('#alert').html(data);
                        hide(alertbox2);
                        unhide(alertbox);

            },
        fail: function(){
                console.error('[skeetsu] Unable to fetch admin stats')
                if (job !== undefined && job !== null) {
                    job.clearInterval();
                }
                return false;
            }
        });
    }

    if (mFetch() !== false)
        var job = setInterval(mFetch, 6000);
   // if (mFetchAlerts() !== false)
    //    var job2 = setInterval(mFetchAlerts, 14000);
});