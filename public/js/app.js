
function page(num){

    $('#pageNumber').val(num);
    $('#searchSubmit').click();

}

function asyncLoad(){
    $('.async-load').each(function(i){
        if(this.attributes["data-async-load"]!=''){
            $.get(this.attributes['data-async-load'].value ,function(data){
                console.log(data);
                this.attributes["src"] = this.attributes["data-async-load"].value;
            });
        }
    });
}