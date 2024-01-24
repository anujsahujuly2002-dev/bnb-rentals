const usersList = document.querySelector(".chat-list"),
chatHeader = document.querySelector('.chatbox'),
incoming_id = document.querySelector('.reciver_id');

setInterval( async()=>{
    const response = await fetch(site_url +"/get-user")
    const results = await response.json();
    if(response.status==200 && results.status){
        let data ='';
        let countTemplate = '';
        for(let index in results.data ){
            if(results.data[index].unread_message >0){
                countTemplate += `<i class="fa fa-bell-o notification-bell" aria-hidden="true"></i> <span class="btn__badge pulse-button ">${results.data[index].unread_message}</span>`
            }
            data += `<a href="javascript:void(0)" class="d-flex align-items-center" onclick="chatBoxOpen('${results.data[index].id}','${results.data[index].image}','${results.data[index].name}')">
                <div class="flex-shrink-0">
                    <img class="img-fluid" src="${results.data[index].image}" alt="user img">
                    <span class="active"></span>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h3>${results.data[index].name}</h3>
                </div>
                ${countTemplate}
            </a>`
        }
        usersList.innerHTML=data;
    }else{
        console.log(results.msg);
    }
},500)

chatBoxOpen = async (id,image,name) =>{
    let template = `<div class="modal-dialog-scrollable">
        <div class="modal-content">
            <div class="msg-head">
                <div class="row">
                    <div class="col-12 chat-headers">
                        <div class="d-flex align-items-center">
                            <span class="chat-icon">
                                <img class="img-fluid" src="https://mehedihtml.com/chatbox/assets/img/arroleftt.svg" alt="image title"></span>
                                <div class="flex-shrink-0">
                                    <img class="img-fluid" src="${image}" alt="user img">
                                </div>
                            <div class="flex-grow-1 ms-3">
                                <h3>${name}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="msg-body" id="msg-body">
                   
                </div>
            </div>
            <div class="send-box">
                <form id="sendMessage">
                    <input type="text" class="form-control input-field" aria-label="message…"  placeholder="Write message…" name="message">
                    <input type="text" class="reciver_id" name="reciver_id" value="${id}" hidden>
                    <button type="submit" disabled onclick="sendMessage(event)"><i class="fa fa-paper-plane" aria-hidden="true"></i> Send</button>
                </form>
            </div>
        </div>
    </div>`;
    chatHeader.innerHTML=template;
    scrollToBottoms()
    
}
function scrollToBottoms(){
    $("#msg-body").scrollTop(1000);
 }
