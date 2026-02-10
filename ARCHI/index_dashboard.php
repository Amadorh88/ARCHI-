


                    <!-- Bienvenida especial para admin -->
                    <div class="welcome-admin">
                        <h2>Bienvenido, <?php echo htmlspecialchars($Nombreusuario); ?>!</h2>
                        <p>Como Administrador del Sistema, tienes acceso completo a todas las funcionalidades y
                            configuraciones.</p>
                    </div>

                    <!-- Estadísticas principales -->
                    <div class="admin-stats">
                        <div class="admin-stat-card">
                            <i class="fas fa-users"></i>
                            <h3><?php echo $estadistica['usuarios']; ?></h3>
                            <p>Usuarios Activos</p>
                        </div>
                        <div class="admin-stat-card">
                            <i class="fas fa-user-friends"></i>
                            <h3><?php echo $estadistica['feligreses']; ?></h3>
                            <p>Feligreses Registrados</p>
                        </div>
                        <div class="admin-stat-card">
                            <i class="fas fa-church"></i>
                            <h3><?php echo $estadistica['parroquias']; ?></h3>
                            <p>Parroquias</p>
                        </div>
                        <div class="admin-stat-card">
                            <i class="fas fa-book"></i>
                            <h3><?php echo $estadistica['catequesis']; ?></h3>
                            <p>Sesiones de Catequesis</p>
                        </div>
                    </div>

                    <!-- Grid de contenido admin -->
                    <div class="admin-grid">
                        <!-- Distribución de usuarios por rol -->
                        <div class="admin-card">
                            <h3><i class="fas fa-chart-pie"></i> Distribución de Usuarios</h3>
                            <div class="user-role-distribution">
                                <?php foreach ($usersByRole as $role => $count):
                                    $roleDisplay = getRoleDisplayName($role === 'secretario' ? 'secretaria' : $role);
                                    ?>
                                    <div class="role-item <?php echo $role === 'secretario' ? 'secretaria' : $role; ?>">
                                        <span><?php echo $roleDisplay; ?></span>
                                        <strong><?php echo $count; ?></strong>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Actividades recientes -->
                        <div class="admin-card">
                            <h3><i class="fas fa-history"></i> Actividad Reciente</h3>
                            <ul class="activity-list">
                                <?php if (empty($recentActivities)): ?>
                                    <li class="activity-item">No hay actividades recientes</li>
                                <?php else: ?>
                                    <?php foreach ($recentActivities as $activity): ?>
                                        <li class="activity-item">
                                            <strong><?php echo htmlspecialchars($activity['usuario_nombre']); ?></strong>
                                            <div><?php echo htmlspecialchars($activity['accion']); ?></div>
                                            <div class="time">
                                                <?php echo date('d/m/Y H:i', strtotime($activity['fecha_registro'])); ?>
                                                <?php if ($activity['ip']): ?>
                                                    <span style="margin-left: 10px;">IP:
                                                        <?php echo htmlspecialchars($activity['ip']); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <!-- Acciones rápidas -->
                        <div class="admin-card">
                            <h3><i class="fas fa-bolt"></i> Acciones Rápidas</h3>
                            <div class="quick-actions">
                                <button class="quick-action-btn" onclick="window.location.href='usuarios.php'">
                                    <i class="fas fa-user-plus"></i>
                                    <span>Nuevo Usuario</span>
                                </button>
                                <button class="quick-action-btn" onclick="window.location.href='backup.php'">
                                    <i class="fas fa-database"></i>
                                    <span>Backup DB</span>
                                </button>
                                <button class="quick-action-btn" onclick="window.location.href='configuracion.php'">
                                    <i class="fas fa-cog"></i>
                                    <span>Configurar</span>
                                </button>
                                <button class="quick-action-btn" onclick="window.location.href='reportes.php'">
                                    <i class="fas fa-chart-bar"></i>
                                    <span>Reportes</span>
                                </button>
                            </div>
                        </div>

                        <!-- Estado del sistema -->
                        <div class="admin-card">
                            <h3><i class="fas fa-server"></i> Estado del Sistema</h3>
                            <div class="system-status">
                                <div class="status-item">
                                    <span>Base de Datos:</span>
                                    <span class="status-badge success">Conectada</span>
                                </div>
                                <div class="status-item">
                                    <span>Espacio Disco:</span>
                                    <span
                                        class="status-badge warning"><?php echo round(disk_free_space("/") / 1024 / 1024 / 1024, 2); ?>
                                        GB libre</span>
                                </div>
                                <div class="status-item">
                                    <span>PHP Version:</span>
                                    <span class="status-badge info"><?php echo phpversion(); ?></span>
                                </div>
                                <div class="status-item">
                                    <span>Último Backup:</span>
                                    <span class="status-badge"><?php echo date('d/m/Y'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
              