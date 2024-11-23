using Microsoft.Maui.Controls;
using System;
using System.Collections.Generic;
using System.IO;
using System.Threading.Tasks;
using therapy.Services;
using therapy.Dtos;

namespace therapy.pages
{
    public partial class Seguimiento : ContentPage
    {
        private readonly ApiService _apiService;
        private byte[] _selectedImageBytes;

        public Seguimiento()
        {
            InitializeComponent();
            _apiService = new ApiService();
            LoadSeguimientos();
        }

        // Método para seleccionar una foto
        private async void OnUploadPhotoClicked(object sender, EventArgs e)
        {
            try
            {
                if (!MediaPicker.IsCaptureSupported)
                {
                    await DisplayAlert("Error", "La selección de fotos no está soportada en este dispositivo.", "OK");
                    return;
                }

                var photoResult = await MediaPicker.PickPhotoAsync(new MediaPickerOptions
                {
                    Title = "Selecciona una foto"
                });

                if (photoResult != null)
                {
                    using var stream = await photoResult.OpenReadAsync();
                    _selectedImageBytes = await ConvertStreamToBytesAsync(stream);

                    // Asignar la imagen a la vista previa
                    SelectedImageView.Source = ImageSource.FromStream(() => new MemoryStream(_selectedImageBytes));
                    SelectedImageView.IsVisible = true;

                    await DisplayAlert("Éxito", "La foto se seleccionó correctamente.", "OK");
                }
            }
            catch (Exception ex)
            {
                await DisplayAlert("Error", $"Ocurrió un error: {ex.Message}", "OK");
            }
        }

        private async void OnSubmitClicked(object sender, EventArgs e)
        {
            if (_selectedImageBytes == null || string.IsNullOrWhiteSpace(CommentEditor.Text))
            {
                await DisplayAlert("Error", "Por favor selecciona una foto y escribe un comentario.", "OK");
                return;
            }

            var clienteId = Preferences.Get("cliente_id", 0);
            if (clienteId == 0)
            {
                await DisplayAlert("Error", "No se pudo obtener la información del cliente.", "OK");
                return;
            }

            try
            {
                // Crear el seguimiento
                var seguimientoDto = new SeguimientoDTO
                {
                    descripcion = CommentEditor.Text,
                    Terapia_idTerapia = clienteId, // Terapia activa del cliente
                    foto_seguimiento = _selectedImageBytes
                };

                var success = await _apiService.AddSeguimientoAsync(seguimientoDto);

                if (success)
                {
                    await DisplayAlert("Éxito", "Tu seguimiento ha sido registrado.", "OK");
                    AddSeguimientoToUI(seguimientoDto.descripcion, _selectedImageBytes);
                    CommentEditor.Text = string.Empty;
                    SelectedImageView.IsVisible = false;
                }
                else
                {
                    await DisplayAlert("Error", "Hubo un problema al registrar tu seguimiento.", "OK");
                }
            }
            catch (Exception ex)
            {
                await DisplayAlert("Error", $"Ocurrió un error: {ex.Message}", "OK");
            }
        }

        private async void LoadSeguimientos()
        {
            var clienteId = Preferences.Get("cliente_id", 0);
            if (clienteId == 0)
            {
                await DisplayAlert("Error", "No se pudo cargar los seguimientos del cliente.", "OK");
                return;
            }

            try
            {
                var seguimientos = await _apiService.GetSeguimientosByClientAsync(clienteId);

                foreach (var seguimiento in seguimientos)
                {
                    AddSeguimientoToUI(seguimiento.descripcion, seguimiento.foto_seguimiento);
                }
            }
            catch (Exception ex)
            {
                await DisplayAlert("Error", $"Ocurrió un error al cargar los seguimientos: {ex.Message}", "OK");
            }
        }

        private void AddSeguimientoToUI(string descripcion, byte[] foto)
        {
            var postFrame = new Frame
            {
                Padding = 10,
                Margin = new Thickness(0, 10),
                BackgroundColor = Color.FromArgb("#EDEBF6"),
                CornerRadius = 10,
                HasShadow = true
            };

            var postContent = new VerticalStackLayout { Spacing = 10 };

            postContent.Add(new Image
            {
                Source = ImageSource.FromStream(() => new MemoryStream(foto)),
                Aspect = Aspect.AspectFit,
                HeightRequest = 150
            });

            postContent.Add(new Label
            {
                Text = descripcion,
                FontSize = 14,
                TextColor = Color.FromArgb("#4f3f9b"),
                HorizontalTextAlignment = TextAlignment.Center
            });

            postFrame.Content = postContent;
            PostsContainer.Children.Add(postFrame);
        }

        private static async Task<byte[]> ConvertStreamToBytesAsync(Stream stream)
        {
            using var memoryStream = new MemoryStream();
            await stream.CopyToAsync(memoryStream);
            return memoryStream.ToArray();
        }
    }
}
