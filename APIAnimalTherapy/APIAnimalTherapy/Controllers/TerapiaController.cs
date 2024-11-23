using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using APIAnimalTherapy.Models;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace APIAnimalTherapy.Controllers
{
    [Route("api/[controller]")]
    [ApiController]
    public class TerapiaController : ControllerBase
    {
        private readonly ApplicationDbContext _context;

        public TerapiaController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: api/Terapia
        [HttpGet]
        public async Task<ActionResult<IEnumerable<Terapia>>> GetTerapias()
        {
            try
            {
                return await _context.Terapias
                    .Include(t => t.cliente)
                    .Include(t => t.especialista)
                    .Include(t => t.animal)
                    .ToListAsync();
            }
            catch (Exception ex)
            {
                return StatusCode(500, $"Error al obtener terapias: {ex.Message}");
            }
        }

        // GET: api/Terapia/5
        [HttpGet("{id}")]
        public async Task<ActionResult<Terapia>> GetTerapia(int id)
        {
            try
            {
                var terapia = await _context.Terapias
                    .Include(t => t.cliente)
                    .Include(t => t.especialista)
                    .Include(t => t.animal)
                    .FirstOrDefaultAsync(t => t.idTerapia == id);

                if (terapia == null)
                {
                    return NotFound();
                }

                return terapia;
            }
            catch (Exception ex)
            {
                return StatusCode(500, $"Error al obtener la terapia: {ex.Message}");
            }
        }

        // POST: api/Terapia
        [HttpPost]
        public async Task<ActionResult<Terapia>> CreateTerapia(Terapia terapia)
        {
            try
            {
                _context.Terapias.Add(terapia);
                await _context.SaveChangesAsync();

                return CreatedAtAction(nameof(GetTerapia), new { id = terapia.idTerapia }, terapia);
            }
            catch (Exception ex)
            {
                return StatusCode(500, $"Error al crear la terapia: {ex.Message}");
            }
        }

        // PUT: api/Terapia/5
        [HttpPut("{id}")]
        public async Task<IActionResult> UpdateTerapia(int id, Terapia terapia)
        {
            if (id != terapia.idTerapia)
            {
                return BadRequest();
            }

            _context.Entry(terapia).State = EntityState.Modified;

            try
            {
                await _context.SaveChangesAsync();
            }
            catch (DbUpdateConcurrencyException)
            {
                if (!TerapiaExists(id))
                {
                    return NotFound();
                }
                else
                {
                    throw;
                }
            }
            catch (Exception ex)
            {
                return StatusCode(500, $"Error al actualizar la terapia: {ex.Message}");
            }

            return NoContent();
        }

        // DELETE: api/Terapia/5
        [HttpDelete("{id}")]
        public async Task<IActionResult> DeleteTerapia(int id)
        {
            try
            {
                var terapia = await _context.Terapias.FindAsync(id);
                if (terapia == null)
                {
                    return NotFound();
                }

                _context.Terapias.Remove(terapia);
                await _context.SaveChangesAsync();

                return NoContent();
            }
            catch (Exception ex)
            {
                return StatusCode(500, $"Error al eliminar la terapia: {ex.Message}");
            }
        }

        // NUEVO: Asignar un animal a una terapia
        [HttpPut("AssignAnimalToTherapy")]
        public async Task<IActionResult> AssignAnimalToTherapy(int clienteId, int animalId)
        {
            try
            {
                var terapia = await _context.Terapias
                    .FirstOrDefaultAsync(t => t.Cliente_cliente_id == clienteId && t.Animal_animal_id == null);

                if (terapia == null)
                {
                    return NotFound("No se encontró una terapia activa para el cliente.");
                }

                var animal = await _context.Animals.FirstOrDefaultAsync(a => a.animal_id == animalId);

                if (animal == null)
                {
                    return NotFound("El animal especificado no existe.");
                }

                if (animal.estado != "disponible")
                {
                    return BadRequest("El animal no está disponible.");
                }

                terapia.Animal_animal_id = animalId;
                animal.estado = "asignado";

                _context.Terapias.Update(terapia);
                _context.Animals.Update(animal);

                await _context.SaveChangesAsync();

                return Ok("El animal fue asignado exitosamente a la terapia.");
            }
            catch (Exception ex)
            {
                return StatusCode(500, $"Error al asignar el animal a la terapia: {ex.Message}");
            }
        }

        private bool TerapiaExists(int id)
        {
            return _context.Terapias.Any(e => e.idTerapia == id);
        }
    }
}
