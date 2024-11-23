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
    public class EspecialistaController : ControllerBase
    {
        private readonly ApplicationDbContext _context;

        public EspecialistaController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: api/Especialista
        [HttpGet]
        public async Task<ActionResult<IEnumerable<Especialista>>> GetEspecialistas()
        {
            return await _context.Especialistas
                .Include(e => e.user) // Incluir la información del usuario relacionado
                .ToListAsync();
        }

        // GET: api/Especialista/5
        [HttpGet("{id}")]
        public async Task<ActionResult<Especialista>> GetEspecialista(int id)
        {
            var especialista = await _context.Especialistas
                .Include(e => e.user) // Incluir la información del usuario relacionado
                .FirstOrDefaultAsync(e => e.especialista_id == id);

            if (especialista == null)
            {
                return NotFound();
            }

            return especialista;
        }

        // POST: api/Especialista
        [HttpPost]
        public async Task<ActionResult<Especialista>> CreateEspecialista(Especialista especialista)
        {
            _context.Especialistas.Add(especialista);
            await _context.SaveChangesAsync();

            return CreatedAtAction(nameof(GetEspecialista), new { id = especialista.especialista_id }, especialista);
        }

        // PUT: api/Especialista/5
        [HttpPut("{id}")]
        public async Task<IActionResult> UpdateEspecialista(int id, Especialista especialista)
        {
            if (id != especialista.especialista_id)
            {
                return BadRequest();
            }

            _context.Entry(especialista).State = EntityState.Modified;

            try
            {
                await _context.SaveChangesAsync();
            }
            catch (DbUpdateConcurrencyException)
            {
                if (!EspecialistaExists(id))
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

        // DELETE: api/Especialista/5
        [HttpDelete("{id}")]
        public async Task<IActionResult> DeleteEspecialista(int id)
        {
            var especialista = await _context.Especialistas.FindAsync(id);
            if (especialista == null)
            {
                return NotFound();
            }

            _context.Especialistas.Remove(especialista);
            await _context.SaveChangesAsync();

            return NoContent();
        }

        private bool EspecialistaExists(int id)
        {
            return _context.Especialistas.Any(e => e.especialista_id == id);
        }
    }
}
