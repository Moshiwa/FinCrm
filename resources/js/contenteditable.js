/*$(document).on('focusout', '.contenteditable', function(e) {
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
});*/

/*function saveField(field, value) {
    let token = $('meta[name="csrf-token"]').attr('content');
    fetch('/field_update/1', {
        method: 'POST',
        body: JSON.stringify({ _token: token })
    })
        .then(response => response.json())
        .then(result => alert(JSON.stringify(result, null, 2)));


    console.log('Send save request ' + field + ' = ' + value);
}*/
