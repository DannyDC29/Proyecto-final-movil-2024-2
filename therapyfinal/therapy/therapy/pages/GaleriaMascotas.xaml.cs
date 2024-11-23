using Microsoft.Maui.Controls;
using System.Collections.ObjectModel;
using System.Threading.Tasks;
using therapy.modelos;
using therapy.Services;

namespace therapy.pages
{
    public partial class GaleriaMascotas : ContentPage
    {
        private readonly ApiService _apiService;
        public ObservableCollection<Animal> ListaDeMascotas { get; set; }

        public GaleriaMascotas()
        {
            InitializeComponent();
            _apiService = new ApiService();

            // Inicializar la colección
            ListaDeMascotas = new ObservableCollection<Animal>();
            BindingContext = this;

            // Cargar los datos de las mascotas
            _ = CargarMascotasAsync();
        }

        private async Task CargarMascotasAsync()
        {
            try
            {
                var mascotas = await _apiService.GetAnimalsNameAndPhotoAsync();
                if (mascotas != null)
                {
                    ListaDeMascotas.Clear();
                    foreach (var mascota in mascotas)
                    {
                        ListaDeMascotas.Add(new Animal
                        {
                            //animal_id = mascota.animal_id,
                            //nombre = mascota.nombre,
                            //foto_animal = mascota.foto_animal
                        });
                    }
                }
                else
                {
                    await DisplayAlert("Error", "No se pudieron cargar las mascotas.", "OK");
                }
            }
            catch (Exception ex)
            {
                await DisplayAlert("Error", $"Ocurrió un problema al cargar las mascotas: {ex.Message}", "OK");
            }
        }
    }
}
