/**
* Template Name: NiceAdmin
* Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
* Updated: Apr 20 2024 with Bootstrap v5.3.3
* Author: BootstrapMade.com
* License: https://bootstrapmade.com/license/
*/

(function() {
  "use strict";

  /**
   * Easy selector helper function
   */
  const select = (el, all = false) => {
    el = el.trim()
    if (all) {
      return [...document.querySelectorAll(el)]
    } else {
      return document.querySelector(el)
    }
  }

  /**
   * Easy event listener function
   */
  const on = (type, el, listener, all = false) => {
    if (all) {
      select(el, all).forEach(e => e.addEventListener(type, listener))
    } else {
      select(el, all).addEventListener(type, listener)
    }
  }

  /**
   * Easy on scroll event listener 
   */
  const onscroll = (el, listener) => {
    el.addEventListener('scroll', listener)
  }

  /**
   * Sidebar toggle
   */
  if (select('.toggle-sidebar-btn')) {
    on('click', '.toggle-sidebar-btn', function(e) {
      select('body').classList.toggle('toggle-sidebar')
    })
  }

  /**
   * Search bar toggle
   */
  if (select('.search-bar-toggle')) {
    on('click', '.search-bar-toggle', function(e) {
      select('.search-bar').classList.toggle('search-bar-show')
    })
  }

  /**
   * Navbar links active state on scroll
   */
  let navbarlinks = select('#navbar .scrollto', true)
  const navbarlinksActive = () => {
    let position = window.scrollY + 200
    navbarlinks.forEach(navbarlink => {
      if (!navbarlink.hash) return
      let section = select(navbarlink.hash)
      if (!section) return
      if (position >= section.offsetTop && position <= (section.offsetTop + section.offsetHeight)) {
        navbarlink.classList.add('active')
      } else {
        navbarlink.classList.remove('active')
      }
    })
  }
  window.addEventListener('load', navbarlinksActive)
  onscroll(document, navbarlinksActive)

  /**
   * Toggle .header-scrolled class to #header when page is scrolled
   */
  let selectHeader = select('#header')
  if (selectHeader) {
    const headerScrolled = () => {
      if (window.scrollY > 100) {
        selectHeader.classList.add('header-scrolled')
      } else {
        selectHeader.classList.remove('header-scrolled')
      }
    }
    window.addEventListener('load', headerScrolled)
    onscroll(document, headerScrolled)
  }

  /**
   * Back to top button
   */
  let backtotop = select('.back-to-top')
  if (backtotop) {
    const toggleBacktotop = () => {
      if (window.scrollY > 100) {
        backtotop.classList.add('active')
      } else {
        backtotop.classList.remove('active')
      }
    }
    window.addEventListener('load', toggleBacktotop)
    onscroll(document, toggleBacktotop)
  }

  /**
   * Initiate tooltips
   */
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
  })

  /**
   * Initiate quill editors
   */
  if (select('.quill-editor-default')) {
    new Quill('.quill-editor-default', {
      theme: 'snow'
    });
  }

  if (select('.quill-editor-bubble')) {
    new Quill('.quill-editor-bubble', {
      theme: 'bubble'
    });
  }

  if (select('.quill-editor-full')) {
    new Quill(".quill-editor-full", {
      modules: {
        toolbar: [
          [{
            font: []
          }, {
            size: []
          }],
          ["bold", "italic", "underline", "strike"],
          [{
              color: []
            },
            {
              background: []
            }
          ],
          [{
              script: "super"
            },
            {
              script: "sub"
            }
          ],
          [{
              list: "ordered"
            },
            {
              list: "bullet"
            },
            {
              indent: "-1"
            },
            {
              indent: "+1"
            }
          ],
          ["direction", {
            align: []
          }],
          ["link", "image", "video"],
          ["clean"]
        ]
      },
      theme: "snow"
    });
  }

  /**
   * Initiate TinyMCE Editor
   */

  const useDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
  const isSmallScreen = window.matchMedia('(max-width: 1023.5px)').matches;

  tinymce.init({
    selector: 'textarea.tinymce-editor',
    plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons accordion',
    editimage_cors_hosts: ['picsum.photos'],
    menubar: 'file edit view insert format tools table help',
    toolbar: "undo redo | accordion accordionremove | blocks fontfamily fontsize | bold italic underline strikethrough | align numlist bullist | link image | table media | lineheight outdent indent| forecolor backcolor removeformat | charmap emoticons | code fullscreen preview | save print | pagebreak anchor codesample | ltr rtl",
    autosave_ask_before_unload: true,
    autosave_interval: '30s',
    autosave_prefix: '{path}{query}-{id}-',
    autosave_restore_when_empty: false,
    autosave_retention: '2m',
    image_advtab: true,
    link_list: [{
        title: 'My page 1',
        value: 'https://www.tiny.cloud'
      },
      {
        title: 'My page 2',
        value: 'http://www.moxiecode.com'
      }
    ],
    image_list: [{
        title: 'My page 1',
        value: 'https://www.tiny.cloud'
      },
      {
        title: 'My page 2',
        value: 'http://www.moxiecode.com'
      }
    ],
    image_class_list: [{
        title: 'None',
        value: ''
      },
      {
        title: 'Some class',
        value: 'class-name'
      }
    ],
    importcss_append: true,
    file_picker_callback: (callback, value, meta) => {
      /* Provide file and text for the link dialog */
      if (meta.filetype === 'file') {
        callback('https://www.google.com/logos/google.jpg', {
          text: 'My text'
        });
      }

      /* Provide image and alt text for the image dialog */
      if (meta.filetype === 'image') {
        callback('https://www.google.com/logos/google.jpg', {
          alt: 'My alt text'
        });
      }

      /* Provide alternative source and posted for the media dialog */
      if (meta.filetype === 'media') {
        callback('movie.mp4', {
          source2: 'alt.ogg',
          poster: 'https://www.google.com/logos/google.jpg'
        });
      }
    },
    height: 600,
    image_caption: true,
    quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
    noneditable_class: 'mceNonEditable',
    toolbar_mode: 'sliding',
    contextmenu: 'link image table',
    skin: useDarkMode ? 'oxide-dark' : 'oxide',
    content_css: useDarkMode ? 'dark' : 'default',
    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
  });

  /**
   * Initiate Bootstrap validation check
   */
  var needsValidation = document.querySelectorAll('.needs-validation')

  Array.prototype.slice.call(needsValidation)
    .forEach(function(form) {
      form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }

        form.classList.add('was-validated')
      }, false)
    })

  /**
   * Initiate Datatables
   */
  const datatables = select('.datatable', true)
  datatables.forEach(datatable => {
    new simpleDatatables.DataTable(datatable, {
      perPageSelect: [5, 10, 15, ["All", -1]],
      columns: [{
          select: 2,
          sortSequence: ["desc", "asc"]
        },
        {
          select: 3,
          sortSequence: ["desc"]
        },
        {
          select: 4,
          cellClass: "green",
          headerClass: "red"
        }
      ]
    });
  })

  /**
   * Autoresize echart charts
   */
  const mainContainer = select('#main');
  if (mainContainer) {
    setTimeout(() => {
      new ResizeObserver(function() {
        select('.echart', true).forEach(getEchart => {
          echarts.getInstanceByDom(getEchart).resize();
        })
      }).observe(mainContainer);
    }, 200);
  }

})();

/*--------------------------------------------------------------
# Serviço
--------------------------------------------------------------*/
document.addEventListener('DOMContentLoaded', function() {
  const tabela = document.getElementById('tabelaEditavel');
  const botoesEditar = tabela.getElementsByClassName('btnEditar');
  const botoesSalvar = tabela.getElementsByClassName('btnSalvar');

  // Adicionar event listeners para todos os botões de editar
  for (let btn of botoesEditar) {
    btn.addEventListener('click', function() {
      let linha = this.closest('tr');
      tornarEditavel(linha);
    });
  }

  // Adicionar event listeners para todos os botões de salvar
  for (let btn of botoesSalvar) {
    btn.addEventListener('click', function() {
      let linha = this.closest('tr');
      salvarLinha(linha);
    });
  }

  function tornarEditavel(linha) {
    // Habilitar edição para todas as células editáveis na linha
    let celulas = linha.querySelectorAll('td[contenteditable="true"]');
    celulas.forEach(function(celula) {
      celula.setAttribute('contenteditable', 'true');
      celula.style.backgroundColor = '#ffffff';
    });

    // Alternar visibilidade dos botões
    linha.querySelector('.btnEditar').style.display = 'none';
    linha.querySelector('.btnSalvar').style.display = 'inline-block';
  }

  function salvarLinha(linha) {
    // Desabilitar edição para todas as células editáveis na linha
    let celulas = linha.querySelectorAll('td[contenteditable="true"]');
    celulas.forEach(function(celula) {
      celula.setAttribute('contenteditable', 'false');
      celula.style.backgroundColor = '#f9f9f9';
    });

    // Alternar visibilidade dos botões
    linha.querySelector('.btnEditar').style.display = 'inline-block';
    linha.querySelector('.btnSalvar').style.display = 'none';
  }
});

/*--------------------------------------------------------------
# Cadastre seu Serviço
--------------------------------------------------------------*/
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('formServico');
  const tabelaServicos = document.getElementById('tabelaServicos').getElementsByTagName('tbody')[0];
  let editingRowIndex = -1; // Índice da linha em edição (-1 significa sem edição)

  // Função para adicionar uma nova linha na tabela
  function addRow(servico, horario, preco, imagem) {
      const newRow = tabelaServicos.insertRow();

      // Cria as células da nova linha
      newRow.innerHTML = `
          <td>${servico}</td>
          <td>${horario}</td>
          <td>${preco}</td>
          <td>${imagem}</td>
          <td>
              <button type="button" class="btn btn-sm btn-primary btn-edit">Editar</button>
              <button type="button" class="btn btn-sm btn-danger btn-delete">Excluir</button>
          </td>
      `;

      // Adiciona event listener para o botão de editar
      const editButton = newRow.querySelector('.btn-edit');
      editButton.addEventListener('click', function () {
          editingRowIndex = newRow.rowIndex - 1; // Ajusta o índice baseado no cabeçalho
          fillFormFields(newRow);
      });

      // Adiciona event listener para o botão de excluir
      const deleteButton = newRow.querySelector('.btn-delete');
      deleteButton.addEventListener('click', function () {
          if (confirm('Tem certeza que deseja excluir este serviço?')) {
              tabelaServicos.deleteRow(newRow.rowIndex - 1); // Remove a linha da tabela
          }
      });
  }

  // Função para preencher o formulário com dados da linha selecionada para edição
  function fillFormFields(row) {
      const cells = row.cells;
      document.getElementById('servico').value = cells[0].textContent;
      document.getElementById('horario').value = cells[1].textContent;
      document.getElementById('preco').value = cells[2].textContent;
      document.getElementById('imagem').value = cells[3].textContent;
  }

  // Event listener para envio do formulário
  form.addEventListener('submit', function (event) {
      event.preventDefault(); // Previne o envio padrão do formulário

      // Captura os valores dos inputs
      const servico = document.getElementById('servico').value;
      const horario = document.getElementById('horario').value;
      const preco = document.getElementById('preco').value;
      const imagem = document.getElementById('imagem').value;

      if (editingRowIndex === -1) {
          // Adiciona nova linha na tabela
          addRow(servico, horario, preco, imagem);
      } else {
          // Edita a linha existente na tabela
          const editedRow = tabelaServicos.rows[editingRowIndex];
          editedRow.cells[0].textContent = servico;
          editedRow.cells[1].textContent = horario;
          editedRow.cells[2].textContent = preco;
          editedRow.cells[3].textContent = imagem;
          editingRowIndex = -1; // Reseta o índice de edição
      }

      // Limpa o formulário após adicionar ou editar na tabela
      form.reset();
  });
});