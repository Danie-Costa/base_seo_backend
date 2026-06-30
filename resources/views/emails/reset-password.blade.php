<h2>Recuperação de Senha</h2>

<p>Olá {{ $user->name ?? '' }},</p>

<p>Clique no botão abaixo para redefinir sua senha:</p>

<p>
    <a href="{{ $url }}"
       style="background:#2563eb;color:#fff;padding:10px 20px;text-decoration:none;border-radius:5px;">
        Redefinir Senha
    </a>
</p>

<p>Se você não solicitou, ignore este email.</p>
