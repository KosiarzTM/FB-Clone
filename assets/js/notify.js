function notify(msg, type, smgTitle = null) {
    let timeout = 3000;

    let notify_class = notify_type(type);
    msg = msg || '';

    if (smgTitle == null)
        switch (type) {
            case "error":
                smgTitle = "Błąd";
                break;
            case "success":
                smgTitle = "Sukces";
                break;
            case "warrning":
                smgTitle = "Ostrzeżenie";
                break;
            case "info":
                smgTitle = "Informacja";
                break;
        }

    let alert_template = `
        <div class="alert ${notify_class} active" role="alert">
                <p class="notify-title">${msg}</p>
        </div>
    `;

    $(alert_template).appendTo('.notifyArea').queue(function () {
        let self = $(this);
        $(this).on('click', (e) => {
            setTimeout(() => { 
                $((e.currentTarget)).remove();
            }, 200)
        })

        setTimeout(function() {
            console.log('remove')
            self.remove();
        }, timeout + 1000);
    });
}


function notify_type(type) {

    let types = {
        'success': 'alert-success',
        'error': 'alert-error',
    };
    return (type in types) ? types[type] : types['info'];
}
