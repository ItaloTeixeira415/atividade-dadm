/**
 * app.js
 * Lógica do front-end (AdminLTE 3) que consome a API PHP de Entregas.
 *
 * IMPORTANTE: ajuste a constante API_BASE para o endereço onde o PHP
 * está rodando (ex.: http://localhost/api_entrega).
 */
const API_BASE = "http://localhost/API_ENTREGA";
 
$(document).ready(function () {
    carregarPagamentos();
    carregarEntregas();

    $('#formEntrega').on('submit', function (e) {
        e.preventDefault();
        salvarEntrega();
    });
});

function carregarEntregas() {
    $.ajax({
        url: `${API_BASE}/entregas.php`,
        method: 'GET',
        dataType: 'json',
        success: function (entregas) {
            renderizarTabela(entregas);
        },
        error: function (xhr) {
            $('#tabelaEntregas').html(
                `<tr><td colspan="5" class="text-center text-danger">
                    Erro ao carregar entregas: ${mensagemErro(xhr)}
                </td></tr>`
            );
        }
    });
}

function renderizarTabela(entregas) {
    if (!entregas || entregas.length === 0) {
        $('#tabelaEntregas').html('<tr><td colspan="5" class="text-center">Nenhuma entrega cadastrada.</td></tr>');
        return;
    }

    let linhas = '';
    entregas.forEach(function (e) {
        const statusClasse = e.pagamento_status === 'aprovado' ? 'badge-success' : 'badge-warning';
        linhas += `
            <tr>
                <td>${e.identrega}</td>
                <td>${e.endereco ?? '-'}</td>
                <td>${e.entregador ?? '-'}</td>
                <td>
                    <span class="badge ${statusClasse} badge-pagamento">
                        ${e.pagamento_forma ?? ('#' + e.pagamento)} ${e.pagamento_valor ? '- R$ ' + parseFloat(e.pagamento_valor).toFixed(2) : ''}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="abrirModalEditar(${e.identrega})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="removerEntrega(${e.identrega})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    $('#tabelaEntregas').html(linhas);
}

function carregarPagamentos() {
    $.ajax({
        url: `${API_BASE}/pagamentos.php`,
        method: 'GET',
        dataType: 'json',
        success: function (pagamentos) {
            let opcoes = '<option value="">Selecione...</option>';
            pagamentos.forEach(function (p) {
                opcoes += `<option value="${p.id}">#${p.id} - ${p.forma} - R$ ${parseFloat(p.valor).toFixed(2)} (${p.status})</option>`;
            });
            $('#pagamento').html(opcoes);
        },
        error: function (xhr) {
            $('#pagamento').html('<option value="">Erro ao carregar pagamentos</option>');
        }
    });
}

function abrirModalNovo() {
    $('#tituloModal').text('Nova Entrega');
    $('#identrega').val('');
    $('#endereco').val('');
    $('#entregador').val('');
    $('#pagamento').val('');
}

function abrirModalEditar(id) {
    $.ajax({
        url: `${API_BASE}/entregas.php?id=${id}`,
        method: 'GET',
        dataType: 'json',
        success: function (e) {
            $('#tituloModal').text('Editar Entrega #' + e.identrega);
            $('#identrega').val(e.identrega);
            $('#endereco').val(e.endereco);
            $('#entregador').val(e.entregador);
            $('#pagamento').val(e.pagamento);
            $('#modalEntrega').modal('show');
        },
        error: function (xhr) {
            alert('Erro ao buscar entrega: ' + mensagemErro(xhr));
        }
    });
}

function salvarEntrega() {
    const id = $('#identrega').val();
    const dados = {
        pagamento: $('#pagamento').val(),
        endereco: $('#endereco').val(),
        entregador: $('#entregador').val()
    };

    const url = id ? `${API_BASE}/entregas.php?id=${id}` : `${API_BASE}/entregas.php`;
    const metodo = id ? 'PUT' : 'POST';

    $.ajax({
        url: url,
        method: metodo,
        contentType: 'application/json',
        data: JSON.stringify(dados),
        success: function () {
            $('#modalEntrega').modal('hide');
            carregarEntregas();
        },
        error: function (xhr) {
            alert('Erro ao salvar entrega: ' + mensagemErro(xhr));
        }
    });
}

function removerEntrega(id) {
    if (!confirm('Tem certeza que deseja remover a entrega #' + id + '?')) {
        return;
    }

    $.ajax({
        url: `${API_BASE}/entregas.php?id=${id}`,
        method: 'DELETE',
        success: function () {
            carregarEntregas();
        },
        error: function (xhr) {
            alert('Erro ao remover entrega: ' + mensagemErro(xhr));
        }
    });
}

function mensagemErro(xhr) {
    try {
        const resp = JSON.parse(xhr.responseText);
        return resp.erro || JSON.stringify(resp.detalhes) || 'Erro desconhecido';
    } catch (e) {
        return 'Erro desconhecido (' + xhr.status + ')';
    }
}