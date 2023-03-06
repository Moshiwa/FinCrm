toggleOptions();
crud.field('type_id').onChange(function(field) {
    toggleOptions()
});

function toggleOptions() {
    let input = crud.field('type_id').input;
    let text = input.options[input.selectedIndex].text;
    if (text !== 'Выборка') {
        crud.field('options').hide();
    } else {
        crud.field('options').show();
    }
}
