document.addEventListener("DOMContentLoaded", ()=>{
  document.querySelector('button[name="DnCadastrar"]')?.addEventListener("click",()=>{
    document.querySelector('.frconten').style.display="block";
  });
  document.querySelector('.remover_cad')?.addEventListener("click",()=>{
    document.querySelector('.frconten').style.display="none";
  });
  document.querySelector('#pesquisa-form').addEventListener('submit', function(event) {
    event.preventDefault();
    if (document.querySelector('#id-inp').value) {
      let div = document.createElement('div');
      div.classList.add("infconten");
      div.style.display="block";
      document.body.appendChild(div);

      gajax(div);
    }
  });

});
function gajax(div) {
  let id = document.querySelector('#id-inp').value;

  let xhr = new XMLHttpRequest();
  xhr.open('GET', '/id-return/' + id);
  xhr.onload = function() {
    if (xhr.status === 200) {
      let res=JSON.parse(xhr.responseText);
      let dc='';
      for (var chave in res) {
        dc+=`<div class="gado-info">`+chave+' : ' +res[chave]+'</div>'
      }
      let btn='';
      if (!res["mensagem"]) {
        btn=`      <button type="button" id="Editar" class="btn btn-lg btn-warning">Editar</button>
        <button type="button" id="Excluir"class="btn btn-lg btn-danger">Excluir</button>`
        if (res['abate']) {
          btn+=`<button type="button" id="Abate" class="btn btn-lg btn-warning">Enviar para abate</button>`;
        }
      }

      div.innerHTML = `
      <div style="background-color:blue;"class="grid-container">
      `+dc+`
      <div>
      `+btn+`
      <button type="button" id="Voltar" class="btn btn-lg btn-success">Voltar</button>
      </div>

      `;
      div.classList.add("infconten");
      div.style.display="block";
      document.body.appendChild(div);
      document.querySelector('#Voltar').addEventListener('click', ()=> {
        document.querySelector('.infconten').remove();

      });
      document.querySelector('#Excluir')?.addEventListener('click', ()=> {
        remover();
      });
      document.querySelector('#Abate')?.addEventListener('click', ()=> {
        abater();

      });
      document.querySelector('#Editar')?.addEventListener('click', ()=> {
        document.querySelector('.infconten').remove();
        let div = document.createElement('div');
        div.classList.add("infconten");
        div.style.display="block";
        div.innerHTML = `
        <form  id="formeditar"style="background-color:blue;"class="grid-container">
        <h1>Preencha os campos que deseja modificar</h1><br>
        <input name="Leite_ed"type="text"class="form-control input-text" placeholder="Alterar quantia de leite semanal">
        <input name="Racao_ed"type="text"class="form-control input-text" placeholder="Alterar quantia de raçao semanal">
        <input name="Peso_ed"type="text"class="form-control input-text" placeholder="Alterar peso">
        <input name="Date_ed"type="date" placeholder="Alterar nascimento">
        <button type="submit" class="btn btn-success btn-lg" id="Atualizar-btn">Enviar</button>
        <button type="button" class="btn btn-success btn-lg" id="Voltar">Voltar</button>

        </form>
        `;
        document.body.appendChild(div);
        document.querySelector('#Voltar').addEventListener('click', ()=> {
          document.querySelector('.infconten').remove();
        });
        document.querySelector('#Atualizar-btn').addEventListener('click', function(event){
          event.preventDefault();
          var formulario = document.querySelectorAll("#formeditar input");
          var dados=[];
          for (var i = 0; i < formulario.length; i++) {
            if (formulario[i].value) {
              var dd={
                'name':formulario[i].name,
                'valor':formulario[i].value,
              }
              dados.push(dd);
            }
          }
          if (!dd.length) {
            editar(document.querySelector('#id-inp').value,dados);
          }
        });
      });
    } else {
      alert('Erro na solicitação. Código de status HTTP: ' + xhr.status);
      document.querySelector('.infconten').remove();

    }
  };
  xhr.send();
}
function remover() {
  let id = document.querySelector('#id-inp').value;
  if (confirm('Tem certeza de que deseja remover?')) {
    let xhr = new XMLHttpRequest();
    xhr.open('DELETE', '/id-delete/' + id);
    xhr.onload = function() {
      if (xhr.status === 200) {
        alert('Gado removido com sucesso.');
        location.reload();
      } else {
        alert('Erro na remoção.' + xhr.status);
      }
    };
    xhr.send();
  }
}
function abater() {
  let id = document.querySelector('#id-inp').value;
  if (confirm('Tem certeza de que deseja enviar para fila de abate?')) {
    let xhr = new XMLHttpRequest();
    xhr.open('DELETE', '/id-cabate/' + id);
    xhr.onload = function() {
      if (xhr.status === 200) {
        alert('Gado enviado para o abate.');
        location.reload();
      } else {
        alert('Erro .' + xhr.status);
      }
    };
    xhr.send();
  }
}
function editar(id,data) {
  fetch('/id-atualize/'+id, {
    method: 'PATCH',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(data)
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Não foi possível atualizar.');

    }
    return response.json();
  })
  .then(data => {
    alert('O gado foi atualizado');
      location.reload();

  })
  .catch(error => {

    alert(error);
  });
}
