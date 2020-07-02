
let flag = false;

$(document).ready( () => {

    $('#deleteButton').click(function() {

        $('input[type=checkbox]').each(function() {
            if ($(this).attr('disabled')) {
                $(this).removeAttr('disabled');
                $('#submitDeleteButton').show(200);
                flag = true;
            }
            else {
                $(this).attr({
                    'disabled': 'disabled'
                });
                this.checked = false;
                $('#submitDeleteButton').hide(200);
                flag = false;
            }
        });

    });

})

let goToImage = (imageName) => {
    if (flag) 
        return;
    location.href = `/webPage/image.php?name=${imageName}`;
}