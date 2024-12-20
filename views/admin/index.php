<?php incluirTemplate('Admin-navbar'); ?>

<main id="admin" class="admin-content">
    <section class="admin-section">
        <h1 style="text-align:center;">Panel de Administración</h1>
        <div class="actions-div" style="display: grid; grid-template-columns: 1fr 1fr; column-gap: 3rem; row-gap: 3rem; justify-items:center; width: fit-content; margin: 0 auto;">
            <a class="admin-link" href="/admin/projects">Gestión de Proyectos</a>
            <a class="admin-link" href="/admin/departments">Gestión de Departamentos</a>
            <a class="admin-link" href="/admin/users">Gestión de Usuarios</a>
            <a class="admin-link" href="/admin/customers">Gestión de Clientes</a>
        </div>
    </section>
</main>