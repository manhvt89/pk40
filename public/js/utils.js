// ManhVT - 19/09/2024
/* Support Modal Dialog style */
(function(modal_support, $) {

    var btn_id, dialog_ref;

    var hide = function() {
        dialog_ref.close();
    };

    var clicked_id = function() {
        return btn_id;
    };

    var submit = function(button_id) {
        return function(dlog_ref) {
            btn_id = button_id;
            dialog_ref = dlog_ref;
            if (button_id == 'submit') {
                $('form', dlog_ref.$modalBody).first().submit();
            }
        }
    };

    var button_class = {
        'submit': 'btn-primary',
        'delete': 'btn-danger',
        'close': 'btn-warning'
    };

    var init = function(selector) {
        var buttons = function(event) {
            var buttons = [];
            var dialog_class = 'modal-dlg';
            $.each($(this).attr('class').split(/\s+/), function(classIndex, className) {
                var width_class = className.split("modal-dlg-");
                if (width_class && width_class.length > 1) {
                    dialog_class = className;
                }
            });

            $.each($(this).data(), function(name, value) {
                var btn_class = name.split("btn");
                if (btn_class && btn_class.length > 1) {
                    var btn_name = btn_class[1].toLowerCase();
                    var is_submit = btn_name == 'submit';
                    buttons.push({
                        id: btn_name,
                        label: value,
                        cssClass: button_class[btn_name],
                        hotkey: is_submit ? 13 : undefined, // Enter.
                        action: submit(btn_name)
                    });
                }
            });
            // Không có nút nào thì mặc định có nút đóng (Close)
            if (!buttons.length) {
                buttons.push({
                    id: 'close',
                    label: '<span class="glyphicon glyphicon-remove"></span>&nbsp;' + lang.line('common_close'), // Thêm icon close
                    cssClass: 'btn-warning',
                    action: function(dialog_ref) {
                        dialog_ref.close();
                    }
                });
            }
            return {
                buttons: buttons.sort(function(a, b) {
                    return ($(b).text()) < ($(a).text()) ? -1 : 1;
                }),
                cssClass: dialog_class
            };
        };

        $(selector).each(function(index, $element) {
            return $(selector).off('click').on('click', function(event) {
                var $link = $(event.target);
                $link = !$link.is("a, button") ? $link.parents("a, button") : $link;

                BootstrapDialog.show({
                    title: $link.attr('title'),
                    backdrop: 'static', // Chặn sự kiện click ra ngoài modal
                    keyboard: false, // Chặn sự kiện nhấn phím Esc
                    closable: false,
                    message: (function() {
                        var node = $('<div></div>');
                        $.get($link.attr('href') || $link.data('href'), function(data) {
                            node.html(data);
                        });
                        return node;
                    })(),
                    buttons: buttons.call(this, event).buttons,
                    cssClass: buttons.call(this, event).cssClass
                });

                return false;
            });
        });
    };

    modal_support.init = init;
    modal_support.submit = submit;
    modal_support.hide = hide;
    modal_support.clicked_id = clicked_id;

})(window.modal_support = window.modal_support || {}, jQuery);