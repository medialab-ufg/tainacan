<script>
    $(function (){});
    //funcao que recupera o tipo para realizar o mapeamento
    function extract_metadata(string){
        if(verify_is_url(string)){
            var split_url = string.split('/');
            //a url quebrada nos caminhos
            if(split_url.length>0){
                //se possuir o handle na url
                if(split_url.indexOf('handle')>=0){
                    get_handle_metadata(split_url);
                }
            }
        }
    }
    //funcao que verifica se eh uma url
    function verify_is_url(url){
        if (url != undefined || url != '') {
            var regExp = /(^|\s)((https?:\/\/)?[\w-]+(\.[\w-]+)+\.?(:\d+)?(\/\S*)?)/gi;
            var match = url.match(regExp);
            if (match) {
                return match;
            }
            else {
                // Do anything for not being valid
                return false;
            }
        }
    }
    // funcao que busca os metadados de um link handle
    /**
     * 
     * @param {array} split_url A url quebrada
     * @returns {undefined}
     */
    function get_handle_metadata(split_url){
       var index = split_url.indexOf('handle');
       var id = split_url[index+2];
       console.log(index,id);
    }
    
</script>
