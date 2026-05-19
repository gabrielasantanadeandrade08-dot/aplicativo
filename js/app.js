// Elite Sistema - JavaScript Principal
// =====================================

const API_URL = 'api/';

// Mostrar mensagem de alerta
function mostrarAlerta(tipo, mensagem) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${tipo}`;
    alertDiv.textContent = mensagem;
    
    const container = document.querySelector('.container') || document.body;
    container.insertBefore(alertDiv, container.firstChild);
    
    setTimeout(() => alertDiv.remove(), 5000);
}

// Função auxiliar para fazer requisições
async function fazerRequisicao(endpoint, dados = {}, metodo = 'POST') {
    try {
        const opcoes = {
            method: metodo,
            credentials: 'include',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Accept': 'application/json',
            }
        };
        
        if (metodo === 'POST') {
            opcoes.body = new URLSearchParams(dados);
        }
        
        const resposta = await fetch(API_URL + endpoint, opcoes);
        const contentType = resposta.headers.get('content-type') || '';
        const texto = await resposta.text();

        if (!resposta.ok) {
            if (resposta.status === 401 || resposta.status === 403) {
                mostrarAlerta('erro', 'Sessão expirada ou não autenticado. Faça login novamente.');
                return { status: 'erro', mensagem: 'Sessão expirada ou não autenticado' };
            }

            console.error('Resposta de erro do servidor:', resposta.status, texto);
            mostrarAlerta('erro', 'Erro ao conectar com o servidor: ' + resposta.status);
            return { status: 'erro', mensagem: 'Erro ao conectar com o servidor' };
        }

        if (contentType.includes('application/json')) {
            try {
                return JSON.parse(texto);
            } catch (erro) {
                console.error('Falha ao fazer parse JSON do servidor:', texto);
                mostrarAlerta('erro', 'Resposta inválida do servidor');
                return { status: 'erro', mensagem: 'Resposta inválida do servidor' };
            }
        }

        console.error('Resposta não JSON:', texto);

        if (resposta.url.includes('login.php') || texto.includes('<title>Login')) {
            mostrarAlerta('erro', 'Sessão expirada ou não autenticado. Faça login novamente.');
            return { status: 'erro', mensagem: 'Sessão expirada ou não autenticado' };
        }

        mostrarAlerta('erro', 'Erro ao conectar com o servidor: resposta inesperada');
        return { status: 'erro', mensagem: 'Resposta inesperada do servidor' };
    } catch (erro) {
        console.error('Erro na requisição:', erro);
        mostrarAlerta('erro', 'Erro ao conectar com o servidor');
        return { status: 'erro', mensagem: 'Erro de conexão' };
    }
}

// LOGIN
async function fazerLogin(event) {
    event.preventDefault();
    
    const email = document.getElementById('email').value;
    const senha = document.getElementById('senha').value;
    
    if (!email || !senha) {
        mostrarAlerta('aviso', 'Preencha todos os campos');
        return;
    }
    
    const resultado = await fazerRequisicao('auth.php', {
        acao: 'login',
        email: email,
        senha: senha
    });
    
    if (resultado.status === 'sucesso') {
        mostrarAlerta('sucesso', resultado.mensagem);
        setTimeout(() => {
            window.location.href = 'dashboard.php';
        }, 1500);
    } else {
        mostrarAlerta('erro', resultado.mensagem);
    }
}

// REGISTRAR
async function fazerRegistro(event) {
    event.preventDefault();
    
    const nome = document.getElementById('nome').value;
    const email = document.getElementById('email').value;
    const senha = document.getElementById('senha').value;
    const confirmar_senha = document.getElementById('confirmar_senha').value;
    
    if (!nome || !email || !senha || !confirmar_senha) {
        mostrarAlerta('aviso', 'Preencha todos os campos');
        return;
    }
    
    const resultado = await fazerRequisicao('auth.php', {
        acao: 'registrar',
        nome: nome,
        email: email,
        senha: senha,
        confirmar_senha: confirmar_senha
    });
    
    if (resultado.status === 'sucesso') {
        mostrarAlerta('sucesso', resultado.mensagem);
        document.querySelector('form').reset();
        setTimeout(() => {
            window.location.href = 'login.php';
        }, 2000);
    } else {
        mostrarAlerta('erro', resultado.mensagem);
    }
}

// LEITOR DE CÓDIGO DE BARRAS
async function lerCodigoBarras() {
    const codigoInput = document.getElementById('codigo-barras');
    const codigo = codigoInput.value.trim();
    
    if (!codigo) {
        mostrarAlerta('aviso', 'Digite ou escaneie um código de barras');
        return;
    }
    
    const resultado = await fazerRequisicao('produtos.php', {
        acao: 'buscar_por_barras',
        codigo_barras: codigo
    });
    
    if (resultado.status === 'sucesso') {
        exibirProduto(resultado.dados);
        codigoInput.value = '';
        codigoInput.focus();
    } else {
        mostrarAlerta('erro', resultado.mensagem);
        codigoInput.value = '';
        codigoInput.focus();
    }
}

// Exibir informações do produto
function exibirProduto(produto) {
    const resultadoDiv = document.getElementById('resultado-leitor');
    
    const precoFormatado = new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(produto.preco);
    
    resultadoDiv.innerHTML = `
        <div class="card">
            <div class="produto-info">
                <div class="info-item">
                    <div class="info-label">Nome do Produto</div>
                    <div class="info-valor">${produto.nome}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Preço</div>
                    <div class="info-valor" style="color: var(--cor-sucesso); font-weight: bold;">
                        ${precoFormatado}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Cadastrado por</div>
                    <div class="info-valor">${produto.usuario_nome || 'N/A'}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Estoque</div>
                    <div class="info-valor">${produto.estoque} unidade(s)</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tamanho</div>
                    <div class="info-valor">${produto.tamanho || 'N/A'}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Cor</div>
                    <div class="info-valor">${produto.cor || 'N/A'}</div>
                </div>
            </div>
            <div style="margin-top: 1.5rem;">
                <button onclick="adicionarAoCarrinho(${produto.id})" class="btn btn-sucesso">
                    + Adicionar ao Carrinho
                </button>
                <button onclick="editarProduto(${produto.id})" class="btn btn-primary">
                    Editar
                </button>
            </div>
        </div>
    `;
    
    resultadoDiv.classList.add('ativo');
}

// GERENCIAMENTO DE PRODUTOS
async function salvarProduto(event) {
    event.preventDefault();
    
    const id = document.getElementById('produto-id').value;
    const acao = id ? 'editar' : 'criar';
    
    const dados = {
        acao: acao,
        codigo_barras: document.getElementById('codigo_barras').value,
        nome: document.getElementById('nome').value,
        descricao: document.getElementById('descricao').value,
        preco: document.getElementById('preco').value,
        estoque: document.getElementById('estoque').value,
        tamanho: document.getElementById('tamanho').value,
        cor: document.getElementById('cor').value,
        marca: document.getElementById('marca').value
    };
    
    if (id) {
        dados.id = id;
    }
    
    const resultado = await fazerRequisicao('produtos.php', dados);
    
    if (resultado.status === 'sucesso') {
        mostrarAlerta('sucesso', resultado.mensagem);
        setTimeout(() => {
            if (id) {
                window.location.reload();
            } else {
                document.querySelector('form').reset();
                listarProdutos();
            }
        }, 1500);
    } else {
        mostrarAlerta('erro', resultado.mensagem);
    }
}

async function salvarDevedor(event) {
    event.preventDefault();

    const nome = document.getElementById('nome_devedor').value.trim();
    const telefone = document.getElementById('telefone_devedor').value.trim();
    const valor = document.getElementById('valor_devedor').value.trim();
    const vencimento = document.getElementById('vencimento_devedor').value;
    const descricao = document.getElementById('descricao_devedor').value.trim();

    if (!nome || !valor || parseFloat(valor) <= 0) {
        mostrarAlerta('aviso', 'Nome e valor são obrigatórios');
        return;
    }

    const resultado = await fazerRequisicao('devedores.php', {
        acao: 'criar',
        nome: nome,
        telefone: telefone,
        valor: valor,
        data_vencimento: vencimento,
        descricao: descricao
    });

    if (resultado.status === 'sucesso') {
        mostrarAlerta('sucesso', resultado.mensagem);
        document.querySelector('form').reset();
        setTimeout(() => {
            window.location.reload();
        }, 1200);
    } else {
        mostrarAlerta('erro', resultado.mensagem);
    }
}

async function deletarDevedor(id) {
    if (!confirm('Tem certeza que deseja remover este fiado?')) {
        return;
    }

    const resultado = await fazerRequisicao('devedores.php', {
        acao: 'deletar',
        id: id
    });

    if (resultado.status === 'sucesso') {
        mostrarAlerta('sucesso', resultado.mensagem);
        setTimeout(() => {
            window.location.reload();
        }, 1200);
    } else {
        mostrarAlerta('erro', resultado.mensagem);
    }
}

async function listarProdutos() {
    const resultado = await fazerRequisicao('produtos.php?acao=listar', {}, 'GET');
    
    if (resultado.status === 'sucesso') {
        const tabela = document.getElementById('tabela-produtos');
        if (tabela) {
            let html = '';
            resultado.dados.forEach(produto => {
                const preco = new Intl.NumberFormat('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                }).format(produto.preco);
                
                html += `
                    <tr>
                        <td>${produto.codigo_barras}</td>
                        <td>${produto.nome}</td>
                        <td>${produto.usuario_nome || 'N/A'}</td>
                        <td>${preco}</td>
                        <td>${produto.estoque}</td>
                        <td>${produto.tamanho || 'N/A'}</td>
                        <td>${produto.cor || 'N/A'}</td>
                        <td>
                            <div class="table-acoes">
                                <button onclick="editarProduto(${produto.id})" class="btn btn-primary btn-small">Editar</button>
                                <button onclick="deletarProduto(${produto.id})" class="btn btn-erro btn-small">Deletar</button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            tabela.innerHTML = html;
        }
    }
}

async function editarProduto(id) {
    window.location.href = `editar-produto.php?id=${id}`;
}

async function deletarProduto(id) {
    if (!confirm('Tem certeza que deseja deletar este produto?')) {
        return;
    }
    
    const resultado = await fazerRequisicao('produtos.php', {
        acao: 'deletar',
        id: id
    });
    
    if (resultado.status === 'sucesso') {
        mostrarAlerta('sucesso', resultado.mensagem);
        setTimeout(() => {
            window.location.reload();
        }, 1500);
    } else {
        mostrarAlerta('erro', resultado.mensagem);
    }
}

// Enviar WhatsApp direto com mensagem preenchida
function cobrarNoWhatsApp(telefone, nome, valor, dataFiado) {
    let digits = (telefone || '').replace(/\D/g, '');
    digits = digits.replace(/^0+/, '');
    if (digits && !digits.startsWith('55')) {
        digits = '55' + digits;
    }

    if (!digits || digits.length < 11) {
        mostrarAlerta('erro', 'Telefone inválido. Atualize com um número de WhatsApp válido.');
        return;
    }

    const valorNumero = Number(valor);
    const valorFormat = Number.isInteger(valorNumero)
        ? valorNumero.toString()
        : valorNumero.toFixed(2).replace('.', ',');

    const mensagem = `Olá ${nome}, você possui um fiado pendente no valor de R$ ${valorFormat}.\nData do fiado: ${dataFiado}.`;
    const link = `https://wa.me/${digits}?text=${encodeURIComponent(mensagem)}`;
    window.open(link, '_blank');
}

// Adicionar ao carrinho (funcionalidade básica)
function adicionarAoCarrinho(produtoId) {
    mostrarAlerta('sucesso', 'Produto adicionado ao carrinho!');
}

// Evento para Enter no leitor de código
document.addEventListener('DOMContentLoaded', function() {
    const codigoInput = document.getElementById('codigo-barras');
    if (codigoInput) {
        codigoInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                lerCodigoBarras();
            }
        });
    }
});
