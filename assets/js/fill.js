$(document).ready(()=>{

    if(localStorage.user !== null) {
        let ud = JSON.parse(localStorage.user)

        $.each(ud,(index,item)=>{
            console.log(index,item)
            $(`input[name='${index}']`).val(item)
        })
    }
})