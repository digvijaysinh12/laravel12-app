console.log("Products JS Loaded");

document.addEventListener("click",function(e){
    if(e.target.classList.contains("btn-outline-danger")){
        console.log("Delete button clicked");
    }
});