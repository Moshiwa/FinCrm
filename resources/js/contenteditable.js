$(document).on('focusout', '.contenteditable', function(e) {
    let target = $(e.target);
    let field = target.data('field');
    let value = target.text();

    saveField(field, value)
});

$(document).on('keydown', '.contenteditable', function(e) {
    if (e.keyCode == 13) {
        e.preventDefault()
        e.target.blur()
        window.getSelection().removeAllRanges()
    }
});

function saveField(field, value) {
    console.log('Send save request ' + field + ' = ' + value);
}
