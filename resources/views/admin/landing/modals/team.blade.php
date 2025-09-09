<!-- Modal Agregar Miembro del Equipo -->
<div class="modal fade" id="addTeamMemberModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Miembro del Equipo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.landing.team.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre Completo</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Cargo/Posición</label>
                                <input type="text" name="position" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Foto del Miembro</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="form-text text-muted">Opcional. Formatos: JPG, PNG, GIF. Máximo 2MB.</small>
                    </div>
                    
                    <h6 class="mt-4 mb-3">Redes Sociales (Opcional)</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-twitter text-primary me-1"></i>Twitter URL
                                </label>
                                <input type="url" name="twitter_url" class="form-control" placeholder="https://twitter.com/usuario">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-facebook text-primary me-1"></i>Facebook URL
                                </label>
                                <input type="url" name="facebook_url" class="form-control" placeholder="https://facebook.com/usuario">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-instagram text-primary me-1"></i>Instagram URL
                                </label>
                                <input type="url" name="instagram_url" class="form-control" placeholder="https://instagram.com/usuario">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-linkedin text-primary me-1"></i>LinkedIn URL
                                </label>
                                <input type="url" name="linkedin_url" class="form-control" placeholder="https://linkedin.com/in/usuario">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Agregar Miembro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Miembro del Equipo -->
<div class="modal fade" id="editTeamMemberModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Miembro del Equipo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editTeamMemberForm" action="{{ route('admin.landing.team.update', 0) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="editTeamMemberId" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre Completo</label>
                                <input type="text" id="editTeamMemberName" name="name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Cargo/Posición</label>
                                <input type="text" id="editTeamMemberPosition" name="position" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea id="editTeamMemberDescription" name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Foto del Miembro</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="form-text text-muted">Opcional. Deja vacío para mantener la imagen actual. Formatos: JPG, PNG, GIF. Máximo 2MB.</small>
                    </div>
                    
                    <h6 class="mt-4 mb-3">Redes Sociales (Opcional)</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-twitter text-primary me-1"></i>Twitter URL
                                </label>
                                <input type="url" id="editTeamMemberTwitterUrl" name="twitter_url" class="form-control" placeholder="https://twitter.com/usuario">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-facebook text-primary me-1"></i>Facebook URL
                                </label>
                                <input type="url" id="editTeamMemberFacebookUrl" name="facebook_url" class="form-control" placeholder="https://facebook.com/usuario">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-instagram text-primary me-1"></i>Instagram URL
                                </label>
                                <input type="url" id="editTeamMemberInstagramUrl" name="instagram_url" class="form-control" placeholder="https://instagram.com/usuario">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-linkedin text-primary me-1"></i>LinkedIn URL
                                </label>
                                <input type="url" id="editTeamMemberLinkedinUrl" name="linkedin_url" class="form-control" placeholder="https://linkedin.com/in/usuario">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Actualizar Miembro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>