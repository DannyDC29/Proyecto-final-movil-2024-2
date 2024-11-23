using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using APIAnimalTherapy.Models;
using APIAnimalTherapy.Dtos;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace APIAnimalTherapy.Controllers
{
    [Route("api/[controller]")]
    [ApiController]
    public class AnimalController : ControllerBase
    {
        private readonly ApplicationDbContext _context;

        public AnimalController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: api/Animal
        [HttpGet]
        public async Task<ActionResult<IEnumerable<Animal>>> GetAnimals()
        {
            return await _context.Animals.ToListAsync();
        }

        // GET: api/Animal/5
        [HttpGet("{id}")]
        public async Task<ActionResult<Animal>> GetAnimal(int id)
        {
            var animal = await _context.Animals.FindAsync(id);

            if (animal == null)
            {
                return NotFound();
            }

            return animal;
        }

        // NUEVO: Obtener solo nombre y foto de las mascotas
        [HttpGet("GetNameAndPhoto")]
        public async Task<ActionResult<IEnumerable<AnimalDTO>>> GetNameAndPhoto()
        {
            var animals = await _context.Animals
                .Select(a => new AnimalDTO
                {
                    animal_id = a.animal_id,
                    nombre = a.nombre,
                    foto_animal = a.foto_animal
                })
                .ToListAsync();

            return Ok(animals);
        }

        // POST: api/Animal
        [HttpPost]
        public async Task<ActionResult<Animal>> CreateAnimal(Animal animal)
        {
            _context.Animals.Add(animal);
            await _context.SaveChangesAsync();

            return CreatedAtAction(nameof(GetAnimal), new { id = animal.animal_id }, animal);
        }

        // PUT: api/Animal/5
        [HttpPut("{id}")]
        public async Task<IActionResult> UpdateAnimal(int id, Animal animal)
        {
            if (id != animal.animal_id)
            {
                return BadRequest();
            }

            _context.Entry(animal).State = EntityState.Modified;

            try
            {
                await _context.SaveChangesAsync();
            }
            catch (DbUpdateConcurrencyException)
            {
                if (!AnimalExists(id))
                {
                    return NotFound();
                }
                else
                {
                    throw;
                }
            }

            return NoContent();
        }

        // DELETE: api/Animal/5
        [HttpDelete("{id}")]
        public async Task<IActionResult> DeleteAnimal(int id)
        {
            var animal = await _context.Animals.FindAsync(id);
            if (animal == null)
            {
                return NotFound();
            }

            _context.Animals.Remove(animal);
            await _context.SaveChangesAsync();

            return NoContent();
        }

        // NUEVO: Filtrar animales disponibles según preferencia y diagnóstico del cliente
        [HttpGet("FilterAnimals/{clienteId}")]
        public async Task<ActionResult<IEnumerable<Animal>>> FilterAnimals(int clienteId)
        {
            try
            {
                // Obtener cliente por ID
                var cliente = await _context.Clientes
                    .FirstOrDefaultAsync(c => c.cliente_id == clienteId);

                if (cliente == null)
                {
                    return NotFound("Cliente no encontrado.");
                }

                // Filtrar animales según preferencia, diagnóstico y estado disponible
                var animalesFiltrados = await _context.Animals
                    .Where(a => a.estado == "disponible" &&
                                a.tipo == cliente.Preferencia_animal &&
                                a.especialidad.Contains(cliente.diagnostico))
                    .ToListAsync();

                if (!animalesFiltrados.Any())
                {
                    return NotFound("No se encontraron animales que coincidan con la preferencia y diagnóstico.");
                }

                return Ok(animalesFiltrados);
            }
            catch (Exception ex)
            {
                return StatusCode(500, $"Error al filtrar los animales: {ex.Message}");
            }
        }

        private bool AnimalExists(int id)
        {
            return _context.Animals.Any(e => e.animal_id == id);
        }
    }
}
